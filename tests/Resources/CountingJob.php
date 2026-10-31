<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;

/**
 * A job that records how many times it was worked, for queue/worker tests.
 */
final class CountingJob implements JobInterface
{
    public int $worked = 0;

    public function __construct(public readonly string $name = 'Rupert')
    {
        //
    }

    public function work(): void
    {
        $this->worked++;
    }
}
