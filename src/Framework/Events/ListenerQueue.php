<?php

namespace Myerscode\Acorn\Framework\Events;

use SplObjectStorage;
use SplPriorityQueue;

class ListenerQueue
{
    protected SplObjectStorage $storage;

    protected SplPriorityQueue $queue;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Gets all listeners.
     *
     * @return ListenerInterface[]
     */
    public function all(): array
    {
        $listeners = [];

        $queue = clone $this->queue;

        if (!$queue->isEmpty()) {
            $queue->top();
        }

        foreach ($queue as $listener) {
            $listeners[] = $listener;
        }

        return $listeners;
    }

    /**
     * Clears the queue.
     */
    public function clear(): void
    {
        unset($this->storage, $this->queue);
        $this->storage = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Checks whether the queue contains the listener.
     */
    public function contains(ListenerInterface $listener): bool
    {
        return $this->storage->contains($listener);
    }

    /**
     * Insert an listener to the queue.
     */
    public function push(ListenerInterface $listener, int $priority): void
    {
        $this->storage->attach($listener, $priority);
        $this->queue->insert($listener, $priority);
    }

    /**
     * Refreshes the status of the queue.
     */
    protected function refresh()
    {
        $this->storage->rewind();

        $this->queue = new SplPriorityQueue();
        foreach ($this->storage as $listener) {
            $priority = $this->storage->getInfo();
            $this->queue->insert($listener, $priority);
        }
    }

    /**
     * Removes an listener from the queue.
     */
    public function remove(ListenerInterface $listener): void
    {
        if ($this->storage->contains($listener)) {
            $this->storage->detach($listener);
            $this->refresh();
        }
    }
}
