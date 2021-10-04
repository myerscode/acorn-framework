<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\ListenerPriorityQueue;

class Dispatcher
{

    /**
     * Collection of listeners.
     *
     * @var ListenerPriorityQueue[]
     */
    protected array $listeners = [];

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param  object  $event
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

            if ($listener instanceof CallableListener || method_exists($listener, 'handle')) {
                $listener->handle($event);
            } else {
                throw new InvalidListenerException();
            }
        }

    }

    public function emit($eventName, $params = null): self
    {
        if (class_exists($eventName)) {
            if (!(($event = new $eventName($params)) instanceof Event)) {
                throw new UnknownEventTypeException();
            }
        } else {
            $event = new class($eventName) extends NamedEvent {
                public function __construct(string $name)
                {
                    parent::__construct($name);
                }
            };
        }

        $this->dispatch($event);

        return $this;
    }

    /**
     * Registries a listener for the event.
     *
     * @param  string  $eventName
     * @param  ListenerInterface|callable  $listener
     * @param  int  $priority
     */
    public function addListener(string $eventName, $listener, int $priority = EventPriority::NORMAL): void
    {

        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = new ListenerPriorityQueue();
        }

        if ($listener instanceof Closure || is_callable($listener)) {
            $callableListener = CallableEventManager::create($listener);
        } else {
            if ($listener instanceof ListenerInterface) {
                $callableListener = $listener;
            } else {
                throw new InvalidListenerException('The listener should be the implementation of the listenerInterface or callable');
            }
        }

        $this->listeners[$eventName]->push($callableListener, $priority);
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
        if ($event instanceof Event) {
            $eventName = $event->eventName();
        } else {
            $eventName = (string) $event;
        }

        return $this->getListeners($eventName);
    }


    /**
     * Gets all listeners of the event or all registered listeners.
     *
     * @param  string|null  $eventName
     *
     * @return array
     */
    public function getListeners(string $eventName = null): array
    {
        if (!is_null($eventName)) {
            return isset($this->listeners[$eventName]) ? $this->listeners[$eventName]->all() : [];
        } else {
            $listeners = [];

            foreach ($this->listeners as $queue) {
                $listeners = array_merge($listeners, $queue->all());
            }

            return $listeners;
        }
    }

    /**
     * Registries a subscriber to then event dispatcher
     *
     * @param SubscriberInterface $subscriber
     */
    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            $this->addListener($eventName, [$subscriber, $action]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        if (empty($this->listeners[$eventName])) {
            return;
        }

        if (is_callable($listener) && false === ($listener = CallableEventManager::findByCallable($listener))) {
            return;
        }

        $this->listeners[$eventName]->remove($listener);
    }

    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $action) {
            $this->removeListener($eventName, [$subscriber, $action]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllListeners($eventName = null)
    {
        if (!is_null($eventName) && isset($this->listeners[$eventName])) {
            $this->listeners[$eventName]->clear();
        } else {
            foreach ($this->listeners as $queue) {
                $queue->clear();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasListener(string $eventName, $listener)
    {
        if (!isset($this->listeners[$eventName])) {
            return false;
        }

        if (is_callable($listener)) {
            $listener = CallableEventManager::findByCallable($listener);
        }

        return $this->listeners[$eventName]->contains($listener);
    }

}
