<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;

class TestEvent extends Event
{
    protected string $eventName = 'test.event';

    public $counter = 0;
}
