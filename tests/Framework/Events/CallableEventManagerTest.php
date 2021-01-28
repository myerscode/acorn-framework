<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use PHPUnit\Framework\TestCase;

class CallableEventManagerTest extends TestCase
{

    public function testFindCallable()
    {
        $dispatcher = new Dispatcher();
        $callable = function () {
        };
        $dispatcher->addListener('my-special-event', $callable);
        $this->assertEquals($callable, CallableEventManager::findByCallable($callable)->getCallable());
    }
}
