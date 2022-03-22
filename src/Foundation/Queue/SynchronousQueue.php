<?php

namespace Myerscode\Acorn\Foundation\Queue;

use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

class SynchronousQueue implements QueueInterface
{
    /**
     * Gets all listeners.
     *
     * @return JobInterface[]
     */
    public function all(): array
    {
        return [];
    }

    /**
     * Clears the queue.
     */
    public function clear(): void
    {
        //
    }

    /**
     * Insert an listener to the queue.
     */
    public function push(JobInterface $job): void
    {
        $job->work();
    }

    /**
     * Removes an listener from the queue.
     */
    public function remove(JobInterface $job): void
    {
        //
    }

    public function pop(): null|JobInterface
    {
        return null;
    }
}
