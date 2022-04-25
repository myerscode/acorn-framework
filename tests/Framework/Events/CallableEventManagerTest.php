<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Testing\Interactions\InteractsWithDispatcher;
use Tests\BaseTestCase;

class CallableEventManagerTest extends BaseTestCase
{
    use InteractsWithDispatcher;

    public function testFindCallable(): void
    {
        $dispatcher = $this->dispatcher();
        $callable = function (): void {
        };
        $dispatcher->addListener('my-special-event', $callable);
        $this->assertEquals($callable, CallableEventManager::findByCallable($callable)->getCallable());
    }
}
