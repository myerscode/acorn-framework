<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestMultiEventListener extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = [
        TestEvent::class,
        'another.test.event'
    ];

    public function handle(Event $event): void
    {
        //
    }
}
