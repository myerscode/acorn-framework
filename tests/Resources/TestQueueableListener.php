<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestQueueableListener extends Listener
{

    protected bool $shouldQueue = true;

    /**
     * @var string[]|string
     */
    protected $listensFor = TestQueueableEvent::class;

    public function handle(Event $event)
    {
        //
    }
}
