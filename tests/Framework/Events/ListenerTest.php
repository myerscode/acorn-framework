<?php

namespace Tests\Framework\Events;

use PHPUnit\Framework\TestCase;
use Tests\Resources\TestEmptyListener;
use Tests\Resources\TestEvent;
use Tests\Resources\TestListener;
use Tests\Resources\TestMultiEventListener;

class ListenerTest extends TestCase
{
    public function testListenerReturnsEventsToSubscribeTo()
    {
        $listener = new TestListener();

        $this->assertEquals(TestEvent::class, $listener->listensFor());

        $listener = new TestMultiEventListener();
        $this->assertEquals([TestEvent::class,'another.test.event'], $listener->listensFor());

        $listener = new TestEmptyListener();
        $this->assertEquals([], $listener->listensFor());
    }
}
