<?php

declare(strict_types=1);

namespace Tests\Resources;

use Override;
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

    #[Override]
    public function push(JobInterface $job): void
    {
        ++$this->pushCounter;
    }
}
