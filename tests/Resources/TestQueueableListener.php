<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestQueueableListener extends Listener
{

    #[\Override]
    protected bool $shouldQueue = true;

    /**
     * @var string[]|string
     */
    #[\Override]
    protected string|array $listensFor = TestQueueableEvent::class;

    public function handle(Event $event): void
    {
        //
    }
}
