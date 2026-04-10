<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\EventInterface;

class TestEvent extends Event implements EventInterface
{
    public int $counter = 0;
    protected string $eventName = 'test.event';
}
