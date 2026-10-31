<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Queue;

use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

/**
 * An in-process, deferred queue: jobs are held in FIFO order and run only when
 * popped (typically by a Worker), not on push.
 *
 * Unlike SynchronousQueue (which runs a job immediately on push), this lets a
 * command enqueue work and defer it, which is what a Worker drains. It does
 * not persist across processes.
 */
class MemoryQueue implements QueueInterface
{
    /** @var list<JobInterface> */
    private array $jobs = [];

    /**
     * @return JobInterface[]
     */
    public function all(): array
    {
        return $this->jobs;
    }

    public function clear(): void
    {
        $this->jobs = [];
    }

    public function pop(): ?JobInterface
    {
        return array_shift($this->jobs);
    }

    public function push(JobInterface $job): void
    {
        $this->jobs[] = $job;
    }

    public function remove(JobInterface $job): void
    {
        $this->jobs = array_values(array_filter(
            $this->jobs,
            static fn (JobInterface $queued): bool => $queued !== $job,
        ));
    }
}
