<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\EventInterface;

class TestEvent extends Event implements EventInterface
{
    protected string $eventName = 'test.event';

    public int $counter = 0;
}
