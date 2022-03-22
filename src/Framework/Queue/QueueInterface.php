<?php

namespace Myerscode\Acorn\Framework\Queue;

use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;

interface QueueInterface
{
    public function all(): array;

    public function clear(): void;

    public function push(JobInterface $job): void;

    public function pop(): null|JobInterface;

    public function remove(JobInterface $job): void;
}
