<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Queue;

use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;

interface QueueInterface
{
    public function all(): array;

    public function clear(): void;

    public function pop(): null|JobInterface;

    public function push(JobInterface $job): void;

    public function remove(JobInterface $job): void;
}
