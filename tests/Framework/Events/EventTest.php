<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\Exception\EventConfigException;
use Myerscode\Acorn\Framework\Events\NamedEvent;
use Tests\BaseTestCase;
use Tests\Resources\TestEvent;

class EventTest extends BaseTestCase
{
    public function testEventCanStopPropagation(): void
    {
        $testEvent = new TestEvent();

        $this->assertFalse($testEvent->isPropagationStopped());

        $testEvent->stopPropagation();

        $this->assertTrue($testEvent->isPropagationStopped());
    }

    public function testEventHasName(): void
    {
        $testEvent = new TestEvent();

        $this->assertEquals(TestEvent::class, $testEvent->eventName());
    }

    public function testNamedEventMustHaveName(): void
    {
        $this->expectException(EventConfigException::class);

        new NamedEvent('');
    }

    public function testNamedEventWithCustomName(): void
    {
        $namedEvent = new NamedEvent('test.event.name');

        $this->assertEquals('test.event.name', $namedEvent->eventName());
    }
}
