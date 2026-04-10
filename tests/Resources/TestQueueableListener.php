<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class TestQueueableListener extends Listener
{
    /**
     * @var string[]|string
     */
    #[Override]
    protected string|array $listensFor = TestQueueableEvent::class;

    #[Override]
    protected bool $shouldQueue = true;

    public function handle(Event $event): void
    {
        //
    }
}
