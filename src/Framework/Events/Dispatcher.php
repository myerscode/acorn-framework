<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Foundation\Queue\Jobs\SynchronousJob;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\QueueInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface as SymfonyEventSubscriberInterface;
use Throwable;

/**
 * Unified event dispatcher for Acorn.
 *
 * Implements Symfony's EventDispatcherInterface so it can be used directly
 * with Symfony Console (eliminating the need for a separate bridging dispatcher).
 * Also supports Acorn's own ListenerInterface, queue integration, and named events.
 */
class Dispatcher implements SymfonyEventDispatcherInterface
{
    protected string $allEventsNamespace = '*';

    /**
     * Instance-scoped callable listener registry.
     *
     * @var CallableListener[]
     */
    protected array $callableListeners = [];

    /** @var string[] */
    protected array $catchAllEventNames = ['*', 'any', 'all'];

    /**
     * Collection of listeners.
     *
     * @var ListenerQueue[]
     */
    protected array $listeners = [];

    public function __construct(protected QueueInterface $queue)
    {
        //
    }

    /**
     * Registers an Acorn subscriber.
     */
    public function addAcornSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            /** @var callable $callable */
            $callable = [$subscriber, $action];
            $this->addListener($eventName, $callable);
        }
    }

    /**
     * Registers a listener for the event.
     *
     * Accepts both Acorn ListenerInterface instances and plain callables.
     * When a callable is provided, it is wrapped in a CallableListener.
     *
     * @param callable|ListenerInterface $listener
     */
    public function addListener(string $eventName, callable|ListenerInterface $listener, int $priority = 0): void
    {
        if (in_array($eventName, $this->catchAllEventNames)) {
            $eventName = $this->allEventsNamespace;
        }

        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = new ListenerQueue();
        }

        if ($listener instanceof ListenerInterface) {
            $newListener = $listener;
        } else {
            $newListener = $this->createCallableListener($listener);
        }

        $this->listeners[$eventName]->push($newListener, $priority);
    }

    /**
     * Registers a Symfony event subscriber.
     */
    public function addSubscriber(SymfonyEventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber::getSubscribedEvents() as $eventName => $params) {
            foreach ($this->normaliseSubscriberParams($params) as [$method, $priority]) {
                // [$subscriber, $method] is a valid callable per the Symfony
                // subscriber contract; the method name is dynamic so PHPStan
                // cannot prove it statically.
                /** @phpstan-ignore argument.type */
                $this->addListener($eventName, Closure::fromCallable([$subscriber, $method]), $priority);
            }
        }
    }

    /**
     * Get all callable listeners registered with this dispatcher.
     *
     * @return CallableListener[]
     */
    public function callableListeners(): array
    {
        return $this->callableListeners;
    }

    /**
     * Clear all callable listeners from this dispatcher instance.
     */
    public function clearCallableListeners(): void
    {
        $this->callableListeners = [];
    }

    /**
     * Create and register a callable listener on this dispatcher instance.
     */
    public function createCallableListener(mixed $callable): CallableListener
    {
        $callableListener = new CallableListener($callable);
        $this->callableListeners[] = $callableListener;

        // Also register with static manager for backward compatibility
        CallableEventManager::register($callableListener);

        return $callableListener;
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * Implements Symfony's EventDispatcherInterface::dispatch() contract:
     * - Accepts any object as an event
     * - Returns the event object
     * - Supports optional $eventName parameter
     *
     * Also supports Acorn's EventInterface for propagation stopping and queue integration.
     */
    public function dispatch(object $event, ?string $eventName = null): object
    {
        if ($eventName === null) {
            $eventName = $event instanceof NamedEventInterface ? $event->eventName() : $event::class;
        }

        $listeners = $this->getListenersForEvent($eventName);

        foreach ($listeners as $listener) {
            // Support Symfony's StoppableEventInterface and Acorn's EventInterface
            if ($event instanceof EventInterface && $event->isPropagationStopped()) {
                break;
            }
            if ($event instanceof \Psr\EventDispatcher\StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            try {
                if ($listener instanceof ListenerInterface) {
                    if ($listener->shouldQueue()) {
                        if ($event instanceof EventInterface) {
                            $this->queue->push(new SynchronousJob($event, $listener));
                        }
                    } else {
                        if ($event instanceof EventInterface) {
                            $listener->handle($event);
                        } else {
                            // For non-Acorn events (e.g. Symfony Console events), invoke the
                            // underlying callable directly if it's a CallableListener
                            if ($listener instanceof CallableListener) {
                                call_user_func($listener->getCallable(), $event);
                            }
                        }
                    }
                }
            } catch (Throwable) {
                // Isolate listener failures — allow remaining listeners to execute.
            }
        }

        return $event;
    }

    /**
     * Emit an event by class name or custom event name.
     *
     * SECURITY: The $eventName parameter must only contain trusted values.
     * If $eventName is a class that exists, it will be instantiated with the given params.
     * Never pass user-controlled input as $eventName — this could trigger arbitrary
     * class constructors for any class extending Event.
     *
     * @param string|class-string $eventName
     * @param mixed $params
     */
    public function emit(string $eventName, mixed $params = null): self
    {
        $params = is_array($params) ? $params : [$params];

        if (class_exists($eventName)) {
            if (!(($event = new $eventName(...$params)) instanceof Event)) {
                throw new UnknownEventTypeException();
            }
        } else {
            $event = new NamedEvent($eventName);
        }

        $this->dispatch($event);

        return $this;
    }

    /**
     * Find a callable listener registered with this dispatcher.
     */
    public function findCallableListener(callable $callable): CallableListener|false
    {
        foreach ($this->callableListeners as $listener) {
            if ($listener->getCallable() === $callable) {
                return $listener;
            }
        }

        return false;
    }

    /**
     * Gets the listener priority for a specific event.
     */
    public function getListenerPriority(string $eventName, callable $listener): ?int
    {
        if (!isset($this->listeners[$eventName])) {
            return null;
        }

        if (!($listener instanceof ListenerInterface)) {
            $found = $this->findCallableListener($listener);
            if ($found === false) {
                return null;
            }
            $listener = $found;
        }

        return $this->listeners[$eventName]->getPriority($listener);
    }

    /**
     * Gets all listeners of a specific event or all listeners sorted by descending priority.
     *
     * @return ($eventName is null ? array<string, list<ListenerInterface>> : list<ListenerInterface>)
     */
    public function getListeners(?string $eventName = null): array
    {
        if (!is_null($eventName)) {
            $listensForAnything = $this->getListenersForAnyEvent();

            if (in_array($eventName, $this->catchAllEventNames)) {
                return $listensForAnything;
            }

            return isset($this->listeners[$eventName]) ? array_merge($this->listeners[$eventName]->all(), $listensForAnything) : $listensForAnything;
        }

        $listeners = [];
        foreach ($this->listeners as $name => $queue) {
            $listeners[$name] = $queue->all();
        }

        return $listeners;
    }

    /**
     * @return ListenerInterface[]
     */
    public function getListenersForAnyEvent(): array
    {
        return isset($this->listeners[$this->allEventsNamespace]) ? $this->listeners[$this->allEventsNamespace]->all() : [];
    }

    /**
     * Get the collection of all registered listeners for an event.
     *
     * @param EventInterface|string $event
     * @return ListenerInterface[]
     */
    public function getListenersForEvent(EventInterface|string $event): array
    {
        $eventName = $event instanceof NamedEventInterface ? $event->eventName() : (is_string($event) ? $event : $event::class);

        $listensForAnything = $this->getListenersForAnyEvent();

        if (in_array($eventName, $this->catchAllEventNames)) {
            return $listensForAnything;
        }

        return isset($this->listeners[$eventName]) ? array_merge($this->listeners[$eventName]->all(), $listensForAnything) : $listensForAnything;
    }

    public function hasListener(string $eventName, ListenerInterface|callable $listener): bool
    {
        if (!isset($this->listeners[$eventName])) {
            return false;
        }

        if (is_callable($listener) && !($listener instanceof ListenerInterface)) {
            $listener = $this->findCallableListener($listener);
        }

        if ($listener === false) {
            return false;
        }

        return $this->listeners[$eventName]->contains($listener);
    }

    /**
     * Checks whether an event has any registered listeners.
     */
    public function hasListeners(?string $eventName = null): bool
    {
        if ($eventName !== null) {
            return !empty($this->getListeners($eventName));
        }

        foreach ($this->listeners as $queue) {
            if ($queue->all() !== []) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes an Acorn subscriber.
     */
    public function removeAcornSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            /** @var callable $callable */
            $callable = [$subscriber, $action];
            $this->removeListener($eventName, $callable);
        }
    }

    public function removeAllListeners(?string $eventName = null): void
    {
        if (!is_null($eventName) && isset($this->listeners[$eventName])) {
            $this->listeners[$eventName]->clear();
        } else {
            foreach ($this->listeners as $listener) {
                $listener->clear();
            }
        }
    }

    /**
     * @param callable|ListenerInterface $listener
     */
    public function removeListener(string $eventName, callable|ListenerInterface $listener): void
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if ($listener instanceof ListenerInterface) {
            $this->listeners[$eventName]->remove($listener);
            return;
        }

        $found = $this->findCallableListener($listener);
        if ($found === false) {
            return;
        }

        $this->listeners[$eventName]->remove($found);
    }

    /**
     * Removes a Symfony event subscriber.
     */
    public function removeSubscriber(SymfonyEventSubscriberInterface $subscriber): void
    {
        foreach ($subscriber::getSubscribedEvents() as $eventName => $params) {
            foreach ($this->normaliseSubscriberParams($params) as [$method]) {
                // See addSubscriber(): dynamic subscriber method name.
                /** @phpstan-ignore argument.type */
                $this->removeListener($eventName, Closure::fromCallable([$subscriber, $method]));
            }
        }
    }

    /**
     * Normalise a Symfony getSubscribedEvents() value into a list of
     * [method, priority] pairs.
     *
     * @param string|array{0: string, 1?: int}|list<array{0: string, 1?: int}> $params
     * @return list<array{0: string, 1: int}>
     */
    private function normaliseSubscriberParams(string|array $params): array
    {
        if (is_string($params)) {
            return [[$params, 0]];
        }

        if (isset($params[0]) && is_string($params[0])) {
            /** @var array{0: string, 1?: int} $params */
            return [[$params[0], (int) ($params[1] ?? 0)]];
        }

        $pairs = [];

        /** @var list<array{0: string, 1?: int}> $params */
        foreach ($params as $listener) {
            $pairs[] = [$listener[0], (int) ($listener[1] ?? 0)];
        }

        return $pairs;
    }
}
