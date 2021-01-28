<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestEmptyListener extends Listener
{
    public function handle(Event $event)
    {
        //
    }
}
