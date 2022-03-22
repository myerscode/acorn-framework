<?php

namespace Tests\Framework\Queue;

use Myerscode\Acorn\Framework\Events\ListenerQueue;
use Tests\BaseTestCase;
use Tests\Resources\TestEmptyListener;
use Tests\Resources\TestListener;

class QueueTest extends BaseTestCase
{
    public function testQueueCanGetAllItemsItContains()
    {
        $queue = new ListenerQueue();

        $listener = new TestEmptyListener();
        $listener2 = new TestListener();

        $queue->push($listener, 0);
        $queue->push($listener2, 10);

        $this->assertEquals([$listener2, $listener], $queue->all());
    }

    public function testQueueCanBeCleared()
    {
        $queue = new ListenerQueue();

        $listener = new TestEmptyListener();

        $queue->push($listener, 0);

        $this->assertEquals([$listener], $queue->all());

        $queue->clear();

        $this->assertEquals([], $queue->all());
    }

    public function testQueueCanCheckIfItContainsSomething()
    {
        $queue = new ListenerQueue();

        $listener = new TestEmptyListener();

        $queue->push($listener, 0);

        $this->assertEquals(true, $queue->contains($listener));
        $this->assertEquals(false, $queue->contains(new TestListener()));
    }

    public function testQueueCanBePushedTo()
    {
        $queue = new ListenerQueue();

        $listener = new TestEmptyListener();

        $queue->push($listener, 0);

        $this->assertEquals([$listener], $queue->all());
    }

    public function testQueueCanHaveItemsRemoved()
    {
        $queue = new ListenerQueue();

        $listener1 = new TestEmptyListener();
        $listener2 = new TestEmptyListener();
        $listener3 = new TestEmptyListener();

        $queue->push($listener1, 0);
        $queue->push($listener2, 0);
        $queue->push($listener3, 0);

        $this->assertEquals([$listener1, $listener2, $listener3], $queue->all());

        $queue->remove($listener2);

        $this->assertEquals([$listener1, $listener3], $queue->all());
    }
}
