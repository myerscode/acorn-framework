<?php

declare(strict_types=1);

namespace Tests\Framework\Events;

use Tests\BaseTestCase;
use Tests\Resources\TestEmptyListener;
use Tests\Resources\TestEvent;
use Tests\Resources\TestListener;
use Tests\Resources\TestMultiEventListener;

final class ListenerTest extends BaseTestCase
{
    public function testListenerReturnsEventsToSubscribeTo(): void
    {
        $listener = new TestListener();

        $this->assertSame(TestEvent::class, $listener->listensFor());

        $listener = new TestMultiEventListener();
        $this->assertSame([TestEvent::class,'another.test.event'], $listener->listensFor());

        $listener = new TestEmptyListener();
        $this->assertSame([], $listener->listensFor());
    }
}
