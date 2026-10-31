<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

use SplObjectStorage;
use SplPriorityQueue;

class ListenerQueue
{
    /** @var SplPriorityQueue<int, ListenerInterface> */
    protected SplPriorityQueue $queue;

    /** @var SplObjectStorage<ListenerInterface, int> */
    protected SplObjectStorage $storage;

    public function __construct()
    {
        $this->storage = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Gets all listeners.
     *
     * @return list<ListenerInterface>
     */
    public function all(): array
    {
        $listeners = [];

        $queue = clone $this->queue;

        if (!$queue->isEmpty()) {
            $queue->top();
        }

        foreach ($queue as $singleQueue) {
            $listeners[] = $singleQueue;
        }

        return $listeners;
    }

    /**
     * Clears the queue.
     */
    public function clear(): void
    {
        $this->storage = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Checks whether the queue contains the listener.
     */
    public function contains(ListenerInterface $listener): bool
    {
        return $this->storage->offsetExists($listener);
    }

    /**
     * Get the priority of a listener, or null if not found.
     */
    public function getPriority(ListenerInterface $listener): ?int
    {
        if (!$this->storage->offsetExists($listener)) {
            return null;
        }

        return $this->storage->offsetGet($listener);
    }

    /**
     * Insert an listener to the queue.
     */
    public function push(ListenerInterface $listener, int $priority): void
    {
        $this->storage->offsetSet($listener, $priority);
        $this->queue->insert($listener, $priority);
    }

    /**
     * Removes a listener from the queue.
     *
     * Note: This operation is O(n) as it requires rebuilding the SplPriorityQueue
     * from the SplObjectStorage. For hot paths with frequent listener removal,
     * consider alternative data structures.
     */
    public function remove(ListenerInterface $listener): void
    {
        if ($this->storage->offsetExists($listener)) {
            $this->storage->offsetUnset($listener);
            $this->refresh();
        }
    }

    /**
     * Refreshes the status of the queue.
     */
    protected function refresh(): void
    {
        $this->storage->rewind();

        $this->queue = new SplPriorityQueue();
        foreach ($this->storage as $singleStorage) {
            $priority = $this->storage->getInfo();
            $this->queue->insert($singleStorage, $priority);
        }
    }
}
