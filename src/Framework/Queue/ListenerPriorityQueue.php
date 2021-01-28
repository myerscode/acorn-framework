<?php

namespace Myerscode\Acorn\Framework\Queue;

use IteratorAggregate;
use Myerscode\Acorn\Framework\Events\ListenerInterface;
use SplObjectStorage;
use SplPriorityQueue;

class ListenerPriorityQueue implements QueueInterface, IteratorAggregate
{
    /**
     * @var  SplObjectStorage
     */
    protected $storage;

    /**
     * @var  SplPriorityQueue
     */
    protected $queue;

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
        foreach ($this->getIterator() as $listener) {
            $listeners[] = $listener;
        }

        return $listeners;
    }

    /**
     * Clears the queue.
     */
    public function clear()
    {
        $this->storage = new SplObjectStorage();
        $this->queue = new SplPriorityQueue();
    }

    /**
     * Checks whether the queue contains the listener.
     *
     * @param  ListenerInterface  $listener
     *
     * @return boolean
     */
    public function contains(ListenerInterface $listener): bool
    {
        return $this->storage->contains($listener);
    }

    /**
     * Clones and returns a iterator.
     *
     * @return  SplPriorityQueue
     */
    public function getIterator(): SplPriorityQueue
    {
        $queue = clone $this->queue;

        if (!$queue->isEmpty()) {
            $queue->top();
        }

        return $queue;
    }

    /**
     * Insert an listener to the queue.
     *
     * @param  ListenerInterface  $listener
     * @param  int  $priority
     */
    public function push(ListenerInterface $listener, int $priority)
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
     *
     * @param  ListenerInterface  $listener
     */
    public function remove(ListenerInterface $listener)
    {
        if ($this->storage->contains($listener)) {
            $this->storage->detach($listener);
            $this->refresh();
        }
    }

}
