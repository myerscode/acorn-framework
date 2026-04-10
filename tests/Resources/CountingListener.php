<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;

class CountingListener extends Listener
{
    protected int $counter = 0;

    public function counter(): int
    {
        return $this->counter;
    }

    #[\Override]
    public function handle(EventInterface $event): void
    {
        ++$this->counter;
    }
}
