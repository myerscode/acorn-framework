<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\EventPriority;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Tests\BaseTestCase;
use Tests\Resources\TestEvent;
use Tests\Resources\TestListener;
use Tests\Resources\TestSubscriber;

class DispatcherTest extends BaseTestCase
{
    public function testInitialize()
    {
        $dispatcher = new Dispatcher();
        $this->assertEmpty($dispatcher->getListeners());
    }

    public function testAddListener()
    {
        $dispatcher = new Dispatcher();
        $this->assertEmpty($dispatcher->getListeners('foo'));
        $dispatcher->addListener('foo', new TestListener());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
    }

    public function testHasListener()
    {
        $dispatcher = new Dispatcher();
        $listener = new TestListener();
        $this->assertFalse($dispatcher->hasListener('foo', $listener));
        $dispatcher->addListener('foo', $listener);
        $this->assertTrue($dispatcher->hasListener('foo', $listener));

        $callback = function () {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertTrue($dispatcher->hasListener('bar', $callback));
    }

    public function testGetListeners()
    {
        $dispatcher = new Dispatcher();
        $listener = new TestListener();
        $dispatcher->addListener('foo', $listener);
        $callback = function () {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertEquals([$listener, CallableEventManager::findByCallable($callback),], $dispatcher->getListeners());
    }

    public function testAddSubscriber()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
        $this->assertCount(1, $dispatcher->getListeners('bar'));
    }

    public function testRemoveListener()
    {
        $dispatcher = new Dispatcher();
        $listener = new TestListener();
        $dispatcher->addListener('bar', $listener);
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('bar', $listener);
        $this->assertCount(0, $dispatcher->getListeners('bar'));
        $dispatcher->addListener('bar', $listener);

        $dispatcher->removeListener('bar', function () {
        });
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('foo', function () {
        });
        $this->assertCount(1, $dispatcher->getListeners('bar'));
    }

    public function testRemoveCallableListener()
    {
        $dispatcher = new Dispatcher();
        $callback = function () {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('bar', $callback);
        $this->assertCount(0, $dispatcher->getListeners('bar'));
    }

    public function testRemoveSubscriber()
    {
        $dispatcher = new Dispatcher();
        $subscriber = new TestSubscriber();
        $dispatcher->addSubscriber($subscriber);
        $this->assertCount(1, $dispatcher->getListeners('foo'));
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeSubscriber($subscriber);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertCount(0, $dispatcher->getListeners('bar'));
    }

    public function testRemoveEventAllListeners()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners('foo');
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertNotEmpty($dispatcher->getListeners('bar'));
    }

    public function testRemoveAllListeners()
    {
        $dispatcher = new Dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners();
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testDispatchEvent()
    {
        $dispatcher = new Dispatcher();
        $counter = 0;
        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter++;
        });
        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter++;
        });
        $dispatcher->dispatch(new TestEvent);
        $this->assertEquals(2, $counter);
    }

    public function testDispatchThrowsErrorIfEventInterfaceIsNotPassed()
    {
        $this->expectException(UnknownEventTypeException::class);
        $dispatcher = new Dispatcher();
        $dispatcher->dispatch(function () {

        });
    }

    public function testDispatchThrowsErrorIfUnknownListenerFound()
    {
        $this->expectException(InvalidListenerException::class);
        $dispatcher =  $this->stub(Dispatcher::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $dispatcher->expects('getListenersForEvent')->andReturn([ new \stdClass()]);
        $dispatcher->dispatch(new TestEvent);

    }

    public function testDispatchCanUseClosureListeners()
    {
        $dispatcher = new Dispatcher();

        $callable = new class() {
            public function __invoke($event)
            {
                $event->counter = 100;
            }
        };

        $event = new TestEvent();

        $dispatcher->addListener(TestEvent::class, $callable);

        $dispatcher->dispatch($event);

        $this->assertEquals(100, $event->counter);
    }

    public function testDispatcherWithPriority()
    {
        $dispatcher = new Dispatcher();

        $counter = [];

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter[] = 'normal';
        }, EventPriority::NORMAL);

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter[] = 'low';
        }, EventPriority::LOW);

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter[] = 'high';
        }, EventPriority::HIGH);

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter[] = 'high';
        }, EventPriority::HIGH);

        $dispatcher->dispatch(new TestEvent);

        $this->assertEquals(['high', 'high', 'normal', 'low'], $counter);
    }

    public function testDispatcherOrdersPriority()
    {
        $dispatcher = new Dispatcher();

        $listener1 = new TestListener();
        $listener2 = new TestListener();
        $listener3 = new TestListener();
        $listener4 = new TestListener();
        $listener5 = new TestListener();
        $listener6 = new TestListener();

        $dispatcher->addListener(TestEvent::class, $listener1, EventPriority::NORMAL);
        $dispatcher->addListener(TestEvent::class, $listener2, EventPriority::HIGH);
        $dispatcher->addListener(TestEvent::class, $listener3, EventPriority::LOW);
        $dispatcher->addListener(TestEvent::class, $listener4, 7);
        $dispatcher->addListener(TestEvent::class, $listener5, 49);
        $dispatcher->addListener(TestEvent::class, $listener6, -31);

        $expected = [
            $listener2,
            $listener5,
            $listener4,
            $listener1,
            $listener6,
            $listener3,
        ];

        $dispatcher->dispatch(new TestEvent);

        $this->assertEquals($expected, $dispatcher->getListeners(TestEvent::class));
    }

    public function testEventDispatchingStopsWhenEventPropagationIsStopped()
    {
        $dispatcher = new Dispatcher();
        $counter = 0;
        $dispatcher->addListener(TestEvent::class, function (Event $event) use (&$counter) {
            $counter++;
            $event->stopPropagation();
        });
        $dispatcher->addListener(TestEvent::class, function (Event $event) use (&$counter) {
            $counter++;
        });
        $dispatcher->dispatch(new TestEvent);
        $this->assertEquals(1, $counter);
    }

    public function testGetListenersForEventUsingEventClass()
    {
        $dispatcher = new Dispatcher();

        $listener1 = new TestListener();
        $listener2 = new TestListener();
        $dispatcher->addListener(TestEvent::class, $listener1);
        $dispatcher->addListener(TestEvent::class, $listener2);

        $expectedListeners = [
            $listener1,
            $listener2,
        ];

        $listeners = $dispatcher->getListenersForEvent(new TestEvent);

        $this->assertCount(2, $listeners);
        $this->assertEquals($expectedListeners, $listeners);

    }

    public function testGetListenersForEventUsingEventName()
    {
        $dispatcher = new Dispatcher();

        $listener1 = new TestListener();
        $listener2 = new TestListener();
        $dispatcher->addListener(TestEvent::class, $listener1);
        $dispatcher->addListener(TestEvent::class, $listener2);

        $expectedListeners = [
            $listener1,
            $listener2,
        ];

        $listeners = $dispatcher->getListenersForEvent(TestEvent::class);

        $this->assertCount(2, $listeners);
        $this->assertEquals($expectedListeners, $listeners);
    }

    public function testCanEmitEventUsingCustomName()
    {
        $dispatcher = new Dispatcher();

        $counter = 0;

        $dispatcher->addListener('test.event', function () use (&$counter) {
            $counter = 7749;
        });

        $dispatcher->emit('test.event');

        $this->assertEquals(7749, $counter);
    }

    public function testCanEmitEventUsingClassName()
    {
        $dispatcher = new Dispatcher();

        $counter = 0;

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter = 7749;
        });

        $dispatcher->emit(TestEvent::class);

        $this->assertEquals(7749, $counter);
    }
}
