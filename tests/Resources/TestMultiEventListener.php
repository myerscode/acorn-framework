<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestMultiEventListener extends Listener
{
    /**
     * @var string[]|string
     */
    #[\Override]
    protected string|array $listensFor = [
        TestEvent::class,
        'another.test.event'
    ];

    public function handle(Event $event): void
    {
        //
    }
}
