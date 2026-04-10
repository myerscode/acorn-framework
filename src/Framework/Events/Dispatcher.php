<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Foundation\Queue\Jobs\SynchronousJob;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

class Dispatcher
{
    protected string $allEventsNamespace = '*';

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
     * Registries a listener for the event.
     */
    public function addListener(string $eventName, ListenerInterface|callable $listener, int $priority = EventPriority::NORMAL): void
    {
        if (in_array($eventName, $this->catchAllEventNames)) {
            $eventName = $this->allEventsNamespace;
        }

        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = new ListenerQueue();
        }

        if ($listener instanceof Closure || is_callable($listener)) {
            $newListener = CallableEventManager::create($listener);
        } else {
            $newListener = $listener;
        }

        $this->listeners[$eventName]->push($newListener, $priority);
    }

    /**
     * Registries a subscriber to then event dispatcher
     */
    public function addSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            /** @var callable $callable */
            $callable = [$subscriber, $action];
            $this->addListener($eventName, $callable);
        }
    }

    /**
     * Dispatches an event to all registered listeners.
     */
    public function dispatch(object $event): void
    {
        if (!($event instanceof EventInterface)) {
            throw new UnknownEventTypeException();
        }

        $listeners = $this->getListenersForEvent($event);

        foreach ($listeners as $listener) {
            if ($event->isPropagationStopped()) {
                break;
            }

            if (!($listener instanceof ListenerInterface)) {
                throw new InvalidListenerException();
            }

            if ($listener->shouldQueue()) {
                $this->queue->push(new SynchronousJob($event, $listener));
            } else {
                $listener->handle($event);
            }
        }
    }

    /**
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
     * Gets all listeners of the event or all registered listeners.
     *
     * @return mixed[]
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
        foreach ($this->listeners as $listener) {
            $listeners = array_merge($listeners, $listener->all());
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
     * Get the collection of all registered listeners for an event
     *
     * @param EventInterface|string $event
     * @return ListenerInterface[]
     */
    public function getListenersForEvent(EventInterface|string $event): array
    {
        $eventName = $event instanceof Event ? $event->eventName() : (is_string($event) ? $event : $event::class);

        return $this->getListeners($eventName);
    }

    public function hasListener(string $eventName, ListenerInterface|callable $listener): bool
    {
        if (!isset($this->listeners[$eventName])) {
            return false;
        }

        if (is_callable($listener)) {
            $listener = CallableEventManager::findByCallable($listener);
        }

        if ($listener === false) {
            return false;
        }

        return $this->listeners[$eventName]->contains($listener);
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

    public function removeListener(string $eventName, ListenerInterface|callable $listener): void
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if (is_callable($listener)) {
            $found = CallableEventManager::findByCallable($listener);
            if ($found === false) {
                return;
            }

            $listener = $found;
        }

        if ($listener instanceof ListenerInterface) { // @phpstan-ignore-line
            $this->listeners[$eventName]->remove($listener);
        }
    }

    public function removeSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            /** @var callable $callable */
            $callable = [$subscriber, $action];
            $this->removeListener($eventName, $callable);
        }
    }
}
