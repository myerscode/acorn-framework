<?php

namespace Tests\Framework\Events;

use stdClass;
use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\EventPriority;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;
use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;
use Myerscode\Acorn\Testing\Interactions\InteractsWithDispatcher;
use Tests\BaseTestCase;
use Tests\Resources\TestEvent;
use Tests\Resources\TestListener;
use Tests\Resources\TestListenserForAll;
use Tests\Resources\TestListensForAll;
use Tests\Resources\TestPropertiesEvent;
use Tests\Resources\TestQueueableEvent;
use Tests\Resources\TestQueueableListener;
use Tests\Resources\TestSinglePropertyEvent;
use Tests\Resources\TestSubscriber;

class DispatcherTest extends BaseTestCase
{
    use InteractsWithDispatcher;

    public function testAddListener(): void
    {
        $dispatcher = $this->newDispatcher();
        $this->assertEmpty($dispatcher->getListeners('foo'));
        $dispatcher->addListener('foo', new TestListener());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
    }

    public function testAddSubscriber(): void
    {
        $dispatcher = $this->newDispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $this->assertCount(1, $dispatcher->getListeners('foo'));
        $this->assertCount(1, $dispatcher->getListeners('bar'));
    }

    public function testCanEmitEventUsingClassName(): void
    {
        $dispatcher = $this->dispatcher();

        $counter = 0;

        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            $counter = 7749;
        });

        $dispatcher->emit(TestEvent::class);

        $this->assertSame(7749, $counter);
    }

    public function testCanEmitEventUsingCustomName(): void
    {
        $dispatcher = $this->dispatcher();

        $counter = 0;

        $dispatcher->addListener('test.event', static function () use (&$counter) : void {
            $counter = 7749;
        });

        $dispatcher->emit('test.event');

        $this->assertSame(7749, $counter);
    }

    public function testCanEmitWithMultipleProperties(): void
    {
        $dispatcher = $this->dispatcher();

        $number = null;
        $word = '';

        $dispatcher->addListener(TestPropertiesEvent::class, static function (TestPropertiesEvent $testPropertiesEvent) use (&$number, &$word) : void {
            $number = $testPropertiesEvent->number;
            $word = $testPropertiesEvent->word;
        });

        $dispatcher->emit(TestPropertiesEvent::class, [7, 'Tor']);

        $this->assertSame('Tor', $word);
        $this->assertSame(7, $number);
    }

    public function testCanEmitWithSingleProperties(): void
    {
        $dispatcher = $this->dispatcher();

        $numberWord = null;

        $dispatcher->addListener(TestSinglePropertyEvent::class, static function (TestSinglePropertyEvent $testSinglePropertyEvent) use (&$numberWord) : void {
            $numberWord = $testSinglePropertyEvent->numberWord;
        });

        $dispatcher->emit(TestSinglePropertyEvent::class, 'Seven');

        $this->assertSame('Seven', $numberWord);
    }

    public function testCanRegisterListenerToFireOnEveryEvent(): void
    {
        $dispatcher = $this->newDispatcher();

        $testListensForAll = new TestListensForAll();

        $dispatcher->addListener('*', $testListensForAll);
        $dispatcher->addListener('all', $testListensForAll);
        $dispatcher->addListener('any', $testListensForAll);

        $this->assertCount(3, $dispatcher->getListeners('*'));
    }

    public function testDispatchCanUseClosureListeners(): void
    {
        $dispatcher = $this->dispatcher();

        $callable = new class() {
            public function __invoke($event)
            {
                $event->counter = 100;
            }
        };

        $testEvent = new TestEvent();

        $dispatcher->addListener(TestEvent::class, $callable);

        $dispatcher->dispatch($testEvent);

        $this->assertSame(100, $testEvent->counter);
    }

    public function testDispatchEvent(): void
    {
        $dispatcher = $this->dispatcher();
        $counter = 0;
        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            ++$counter;
        });
        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            ++$counter;
        });
        $dispatcher->dispatch(new TestEvent);
        $this->assertSame(2, $counter);
    }

    public function testDispatchThrowsErrorIfEventInterfaceIsNotPassed(): void
    {
        $this->expectException(UnknownEventTypeException::class);
        $dispatcher = $this->dispatcher();
        $dispatcher->dispatch(static function () : void {
        });
    }

    public function testDispatchThrowsErrorIfUnknownListenerFound(): void
    {
        $this->expectException(InvalidListenerException::class);
        $legacyMock = $this->stub(Dispatcher::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $legacyMock->expects('getListenersForEvent')->andReturn([new stdClass()]);
        $legacyMock->dispatch(new TestEvent);
    }

    public function testDispatcherOrdersPriority(): void
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

    public function testDispatcherWithPriority(): void
    {
        $dispatcher = $this->dispatcher();

        $counter = [];

        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            $counter[] = 'normal';
        }, EventPriority::NORMAL);

        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            $counter[] = 'low';
        }, EventPriority::LOW);

        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            $counter[] = 'high';
        }, EventPriority::HIGH);

        $dispatcher->addListener(TestEvent::class, static function () use (&$counter) : void {
            $counter[] = 'high';
        }, EventPriority::HIGH);

        $dispatcher->dispatch(new TestEvent);

        $this->assertSame(['high', 'high', 'normal', 'low'], $counter);
    }

    public function testEventDispatchingStopsWhenEventPropagationIsStopped(): void
    {
        $dispatcher = $this->dispatcher();
        $counter = 0;
        $dispatcher->addListener(TestEvent::class, static function (Event $event) use (&$counter) : void {
            ++$counter;
            $event->stopPropagation();
        });
        $dispatcher->addListener(TestEvent::class, static function (Event $event) use (&$counter) : void {
            ++$counter;
        });
        $dispatcher->dispatch(new TestEvent);
        $this->assertSame(1, $counter);
    }

    public function testGetListeners(): void
    {
        $dispatcher = $this->newDispatcher();
        $testListener = new TestListener();
        $dispatcher->addListener('foo', $testListener);
        $callback = static function () : void {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertEquals([$testListener, CallableEventManager::findByCallable($callback),], $dispatcher->getListeners());
    }

    public function testGetListenersForEventUsingEventClass(): void
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

    public function testGetListenersForEventUsingEventName(): void
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

    public function testHasListener(): void
    {
        $dispatcher = $this->newDispatcher();
        $testListener = new TestListener();
        $this->assertFalse($dispatcher->hasListener('foo', $testListener));
        $dispatcher->addListener('foo', $testListener);
        $this->assertTrue($dispatcher->hasListener('foo', $testListener));

        $callback = static function () : void {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertTrue($dispatcher->hasListener('bar', $callback));
    }

    public function testInitialize(): void
    {
        $dispatcher = $this->newDispatcher();
        $this->assertEmpty($dispatcher->getListeners());
    }

    public function testRemoveAllListeners(): void
    {
        $dispatcher = $this->dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners();
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertCount(0, $dispatcher->getListeners('foo'));
    }

    public function testRemoveCallableListener(): void
    {
        $dispatcher = $this->newDispatcher();
        $callback = static function () : void {
        };
        $dispatcher->addListener('bar', $callback);
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('bar', $callback);
        $this->assertCount(0, $dispatcher->getListeners('bar'));
    }

    public function testRemoveEventAllListeners(): void
    {
        $dispatcher = $this->dispatcher();
        $dispatcher->addSubscriber(new TestSubscriber());
        $dispatcher->removeAllListeners('foo');
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertNotEmpty($dispatcher->getListeners('bar'));
    }

    public function testRemoveListener(): void
    {
        $dispatcher = $this->newDispatcher();
        $testListener = new TestListener();
        $dispatcher->addListener('bar', $testListener);
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('bar', $testListener);
        $this->assertCount(0, $dispatcher->getListeners('bar'));
        $dispatcher->addListener('bar', $testListener);

        $dispatcher->removeListener('bar', static function () : void {
        });
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeListener('foo', static function () : void {
        });
        $this->assertCount(1, $dispatcher->getListeners('bar'));
    }

    public function testRemoveSubscriber(): void
    {
        $testSubscriber = null;
        $dispatcher = $this->dispatcher();
        $testSubscriber = new TestSubscriber();
        $dispatcher->addSubscriber($testSubscriber);
        $this->assertCount(1, $dispatcher->getListeners('foo'));
        $this->assertCount(1, $dispatcher->getListeners('bar'));
        $dispatcher->removeSubscriber($testSubscriber);
        $this->assertCount(0, $dispatcher->getListeners('foo'));
        $this->assertCount(0, $dispatcher->getListeners('bar'));
    }

    public function testWillPushAnonymousQueueableEventToQueue(): void
    {
        $queue = new class extends SynchronousQueue {
            public int $counter = 0;

            public function push(JobInterface $job): void
            {
                ++$this->counter;
            }
        };

        $dispatcher = $this->newDispatcher($queue);

        $callable = new class {
            public bool $shouldQueue = true;

            public function __invoke()
            {
                //
            }
        };

        $dispatcher->addListener(TestQueueableEvent::class, $callable);
        $dispatcher->emit(TestQueueableEvent::class);

        $this->assertSame(1, $queue->counter);
    }

    public function testWillPushQueueableEventToQueue(): void
    {
        $queue = new class extends SynchronousQueue {
            public int $counter = 0;

            public function push(JobInterface $job): void
            {
                ++$this->counter;
            }
        };
        $dispatcher = $this->newDispatcher($queue);
        $dispatcher->addListener(TestQueueableEvent::class, new TestQueueableListener());
        $dispatcher->emit(TestQueueableEvent::class);

        $this->assertSame(1, $queue->counter);
    }
}
