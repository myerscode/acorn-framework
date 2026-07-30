<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class TestQueueableListener extends Listener
{
    /**
     * @var string[]|string
     */
#[Override]
    protected bool $shouldQueue = true;

    #[Override]
    public function handle(EventInterface $event): void
    {
        //
    }

    public function listensFor(): array|string
    {
        return TestQueueableEvent::class;
    }
}
