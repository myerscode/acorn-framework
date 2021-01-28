<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestListener extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = TestEvent::class;

    public function handle(Event $event)
    {
        //
    }
}
