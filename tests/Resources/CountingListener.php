<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Listener;

class CountingListener extends Listener
{
    protected int $counter = 0;

    public function counter(): int
    {
        return $this->counter;
    }

    public function handle(): void
    {
        ++$this->counter;
    }
}
