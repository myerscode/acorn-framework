<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\EventPriority;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;
use Tests\BaseTestCase;
use Tests\Resources\TestEvent;
use Tests\Resources\TestListener;
use Tests\Resources\TestPropertiesEvent;
use Tests\Resources\TestQueueableEvent;
use Tests\Resources\TestQueueableListener;
use Tests\Resources\TestSinglePropertyEvent;
use Tests\Resources\TestSubscriber;

class DispatcherTest extends BaseTestCase
{
    public function testInitialize()
    {
        $dispatcher = $this->dispatcher();
        $this->assertEmpty($dispatcher->getListeners());
    }

    public function testAddListener()
    {
        $dispatcher = $this->dispatcher();
        $this->assertEmpty($dispatcher->getListeners('foo'));
        $dispatcher->addListener('foo', new TestListener());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
    }

    public function testHasListener()
    {
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();
        $listener = new TestListener();
        $dispatcher->addListener('foo', $listener);
        $callback = function () {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertEquals([$listener, CallableEventManager::findByCallable($callback),], $dispatcher->getListeners());
    }

    public function testAddSubscriber()
    {
        $dispatcher = $this->dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
        $this->assertCount(1, $dispatcher->getListeners('bar'));
    }

    public function testRemoveListener()
    {
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();
        $callback = function () {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('bar', $callback);
        $this->assertCount(0, $dispatcher->getListeners('bar'));
    }

    public function testRemoveSubscriber()
    {
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners('foo');
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertNotEmpty($dispatcher->getListeners('bar'));
    }

    public function testRemoveAllListeners()
    {
        $dispatcher = $this->dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners();
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testDispatchEvent()
    {
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();

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
        $dispatcher = $this->dispatcher();

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
        $dispatcher = $this->dispatcher();

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
        $dispatcher = $this->dispatcher();
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
        $dispatcher = $this->dispatcher();

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
        $dispatcher = $this->dispatcher();

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
        $dispatcher = $this->dispatcher();

        $counter = 0;

        $dispatcher->addListener('test.event', function () use (&$counter) {
            $counter = 7749;
        });

        $dispatcher->emit('test.event');

        $this->assertEquals(7749, $counter);
    }

    public function testCanEmitEventUsingClassName()
    {
        $dispatcher = $this->dispatcher();

        $counter = 0;

        $dispatcher->addListener(TestEvent::class, function () use (&$counter) {
            $counter = 7749;
        });

        $dispatcher->emit(TestEvent::class);

        $this->assertEquals(7749, $counter);
    }

    public function testCanEmitWithSingleProperties()
    {
        $dispatcher = $this->dispatcher();

        $numberWord = null;

        $dispatcher->addListener(TestSinglePropertyEvent::class, function (TestSinglePropertyEvent $event) use (&$numberWord) {
            $numberWord = $event->numberWord;
        });

        $dispatcher->emit(TestSinglePropertyEvent::class, 'Seven');

        $this->assertEquals('Seven', $numberWord);
    }

    public function testCanEmitWithMultipleProperties()
    {
        $dispatcher = $this->dispatcher();

        $number = null;
        $word = '';

        $dispatcher->addListener(TestPropertiesEvent::class, function (TestPropertiesEvent $event) use (&$number, &$word) {
            $number = $event->number;
            $word = $event->word;
        });

        $dispatcher->emit(TestPropertiesEvent::class, [7, 'Tor']);

        $this->assertEquals('Tor', $word);
        $this->assertEquals(7, $number);
    }

    public function testWillPushQueueableEventToQueue()
    {
        $queue = new class extends SynchronousQueue {
            public int $counter = 0;
            public function push(JobInterface $job): void
            {
                $this->counter++;
            }
        };
        $dispatcher = $this->dispatcher($queue);
        $dispatcher->addListener(TestQueueableEvent::class, new TestQueueableListener());
        $dispatcher->emit(TestQueueableEvent::class);

        $this->assertEquals(1, $queue->counter);
    }

    public function testWillPushAnonymousQueueableEventToQueue()
    {
        $queue = new class extends SynchronousQueue {
            public int $counter = 0;
            public function push(JobInterface $job): void
            {
                $this->counter++;
            }
        };

        $dispatcher = $this->dispatcher($queue);

        $callable = new class {
            public $shouldQueue = true;
            public function __invoke()
            {
                //
            }
        };

        $dispatcher->addListener(TestQueueableEvent::class, $callable);
        $dispatcher->emit(TestQueueableEvent::class);

        $this->assertEquals(1, $queue->counter);
    }
}
