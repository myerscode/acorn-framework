<?php

declare(strict_types=1);

namespace Tests\Foundation\Queue;

use Myerscode\Acorn\Foundation\Queue\MemoryQueue;
use Myerscode\Acorn\Framework\Queue\Worker;
use Tests\BaseTestCase;
use Tests\Resources\CountingJob;

final class MemoryQueueTest extends BaseTestCase
{
    public function testPopsInFifoOrder(): void
    {
        $queue = new MemoryQueue();
        $first = new CountingJob('Rupert');
        $second = new CountingJob('Gerald');

        $queue->push($first);
        $queue->push($second);

        $this->assertSame($first, $queue->pop());
        $this->assertSame($second, $queue->pop());
        $this->assertNull($queue->pop());
    }
    public function testPushDefersWorkUntilPopped(): void
    {
        $queue = new MemoryQueue();
        $job = new CountingJob('Rupert');

        $queue->push($job);

        // Deferred: push alone does not run the job.
        $this->assertSame(0, $job->worked);
        $this->assertCount(1, $queue->all());

        $popped = $queue->pop();
        $this->assertSame($job, $popped);
        $this->assertSame([], $queue->all());
    }

    public function testRemoveAndClear(): void
    {
        $queue = new MemoryQueue();
        $rupert = new CountingJob('Rupert');
        $gerald = new CountingJob('Gerald');
        $queue->push($rupert);
        $queue->push($gerald);

        $queue->remove($rupert);
        $this->assertSame([$gerald], array_values($queue->all()));

        $queue->clear();
        $this->assertSame([], $queue->all());
    }

    public function testWorkerDrainsTheQueue(): void
    {
        $queue = new MemoryQueue();
        $rupert = new CountingJob('Rupert');
        $gerald = new CountingJob('Gerald');
        $arthur = new CountingJob('Arthur');
        $queue->push($rupert);
        $queue->push($gerald);
        $queue->push($arthur);

        $processed = new Worker($queue)->work();

        $this->assertSame(3, $processed);
        $this->assertSame(1, $rupert->worked);
        $this->assertSame(1, $gerald->worked);
        $this->assertSame(1, $arthur->worked);
        $this->assertSame([], $queue->all());
    }

    public function testWorkerWorkNextProcessesOneJob(): void
    {
        $queue = new MemoryQueue();
        $queue->push(new CountingJob('Rupert'));

        $worker = new Worker($queue);

        $this->assertTrue($worker->workNext());
        $this->assertFalse($worker->workNext());
    }
}
