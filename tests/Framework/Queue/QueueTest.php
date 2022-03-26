<?php

namespace Tests\Framework\Queue;

use Myerscode\Acorn\Framework\Events\ListenerQueue;
use Tests\BaseTestCase;
use Tests\Resources\TestEmptyListener;
use Tests\Resources\TestListener;

class QueueTest extends BaseTestCase
{
    public function testQueueCanGetAllItemsItContains(): void
    {
        $listenerQueue = null;
        $testEmptyListener = null;
        $testListener = null;
        $listenerQueue = new ListenerQueue();

        $testEmptyListener = new TestEmptyListener();
        $testListener = new TestListener();

        $listenerQueue->push($testEmptyListener, 0);
        $listenerQueue->push($testListener, 10);

        $this->assertEquals([$testListener, $testEmptyListener], $listenerQueue->all());
    }

    public function testQueueCanBeCleared(): void
    {
        $listenerQueue = null;
        $testEmptyListener = null;
        $listenerQueue = new ListenerQueue();

        $testEmptyListener = new TestEmptyListener();

        $listenerQueue->push($testEmptyListener, 0);

        $this->assertEquals([$testEmptyListener], $listenerQueue->all());

        $listenerQueue->clear();

        $this->assertEquals([], $listenerQueue->all());
    }

    public function testQueueCanCheckIfItContainsSomething(): void
    {
        $listenerQueue = null;
        $testEmptyListener = null;
        $listenerQueue = new ListenerQueue();

        $testEmptyListener = new TestEmptyListener();

        $listenerQueue->push($testEmptyListener, 0);

        $this->assertEquals(true, $listenerQueue->contains($testEmptyListener));
        $this->assertEquals(false, $listenerQueue->contains(new TestListener()));
    }

    public function testQueueCanBePushedTo(): void
    {
        $listenerQueue = null;
        $testEmptyListener = null;
        $listenerQueue = new ListenerQueue();

        $testEmptyListener = new TestEmptyListener();

        $listenerQueue->push($testEmptyListener, 0);

        $this->assertEquals([$testEmptyListener], $listenerQueue->all());
    }

    public function testQueueCanHaveItemsRemoved(): void
    {
        $listenerQueue = null;
        $listenerQueue = new ListenerQueue();

        $listener1 = new TestEmptyListener();
        $listener2 = new TestEmptyListener();
        $listener3 = new TestEmptyListener();

        $listenerQueue->push($listener1, 0);
        $listenerQueue->push($listener2, 0);
        $listenerQueue->push($listener3, 0);

        $this->assertEquals([$listener1, $listener2, $listener3], $listenerQueue->all());

        $listenerQueue->remove($listener2);

        $this->assertEquals([$listener1, $listener3], $listenerQueue->all());
    }
}
