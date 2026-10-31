<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Queue;

/**
 * Drains a queue by popping jobs and running them in order.
 *
 * With a SynchronousQueue (jobs run on push) there is nothing to drain, so a
 * Worker is a no-op; with a deferred queue (e.g. MemoryQueue) it processes the
 * pending jobs.
 */
class Worker
{
    public function __construct(private readonly QueueInterface $queue)
    {
        //
    }

    /**
     * Process every pending job currently on the queue, returning the number
     * of jobs run. Jobs enqueued while draining are processed too.
     */
    public function work(): int
    {
        $processed = 0;

        while (($job = $this->queue->pop()) instanceof \Myerscode\Acorn\Framework\Queue\Jobs\JobInterface) {
            $job->work();
            $processed++;
        }

        return $processed;
    }

    /**
     * Process a single pending job, returning whether one was run.
     */
    public function workNext(): bool
    {
        $job = $this->queue->pop();

        if ($job === null) {
            return false;
        }

        $job->work();

        return true;
    }
}
