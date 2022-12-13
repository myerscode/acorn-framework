<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Foundation\Queue\Jobs\SynchronousJob;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

class Dispatcher
{
    protected array $catchAllEventNames = ['*', 'any', 'all'];

    protected string $allEventsNamespace = '*';

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
            $this->addListener($eventName, [$subscriber, $action]);
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
            if ($listener instanceof ListenerInterface) {
                if ($event->isPropagationStopped()) {
                    break;
                }

                if ($listener->shouldQueue()) {
                    $this->queue->push(new SynchronousJob($event, $listener));
                } else {
                    $listener->handle($event);
                }
            } else {
                throw new InvalidListenerException();
            }
        }
    }

    public function emit($eventName, $params = null): self
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
     * @param  string|null  $eventName
     *
     * @return mixed[]
     */
    public function getListeners(string $eventName = null): array
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

    public function getListenersForAnyEvent(): array
    {
        return isset($this->listeners[$this->allEventsNamespace]) ? $this->listeners[$this->allEventsNamespace]->all() : [];
    }

    /**
     * Get the collection of all registered listeners for an event
     *
     * @param $event
     *
     * @return ListenerInterface[]
     */
    public function getListenersForEvent($event): array
    {
        $eventName = $event instanceof Event ? $event->eventName() : (string)$event;

        return $this->getListeners($eventName);
    }

    public function hasListener(string $eventName, $listener): bool
    {
        if (!isset($this->listeners[$eventName])) {
            return false;
        }

        if (is_callable($listener)) {
            $listener = CallableEventManager::findByCallable($listener);
        }

        return $this->listeners[$eventName]->contains($listener);
    }

    public function removeAllListeners($eventName = null): void
    {
        if (!is_null($eventName) && isset($this->listeners[$eventName])) {
            $this->listeners[$eventName]->clear();
        } else {
            foreach ($this->listeners as $listener) {
                $listener->clear();
            }
        }
    }

    public function removeListener($eventName, $listener): void
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if (is_callable($listener) && false === ($listener = CallableEventManager::findByCallable($listener))) {
            return;
        }

        $this->listeners[$eventName]->remove($listener);
    }

    public function removeSubscriber(SubscriberInterface $subscriber): void
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            $this->removeListener($eventName, [$subscriber, $action]);
        }
    }

}
