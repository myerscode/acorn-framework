<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Tests\BaseTestCase;

class CallableEventManagerTest extends BaseTestCase
{

    public function testFindCallable()
    {
        $dispatcher = $this->dispatcher();
        $callable = function () {
        };
        $dispatcher->addListener('my-special-event', $callable);
        $this->assertEquals($callable, CallableEventManager::findByCallable($callable)->getCallable());
    }
}
