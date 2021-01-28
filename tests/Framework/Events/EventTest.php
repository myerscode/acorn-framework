<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\Exception\EventConfigException;
use Myerscode\Acorn\Framework\Events\NamedEvent;
use PHPUnit\Framework\TestCase;
use Tests\Resources\TestEvent;

class EventTest extends TestCase
{
    public function testEventHasName()
    {
        $event = new TestEvent();

        $this->assertEquals(TestEvent::class, $event->eventName());
    }

    public function testNamedEventWithCustomName()
    {
        $event = new NamedEvent('test.event.name');

        $this->assertEquals('test.event.name', $event->eventName());
    }

    public function testNamedEventMustHaveName()
    {
        $this->expectException(EventConfigException::class);

        new NamedEvent('');
    }

    public function testEventCanStopPropagation()
    {
        $event = new TestEvent();

        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}
