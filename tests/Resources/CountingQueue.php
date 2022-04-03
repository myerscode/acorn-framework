<?php

namespace Tests\Resources;

use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;

class CountingQueue extends SynchronousQueue
{
    protected int $pushCounter = 0;

    protected int $counter = 0;

    public function pushCounter(): int
    {
        return $this->pushCounter;
    }

    public function push(JobInterface $job): void
    {
        $this->pushCounter++;
    }
}
