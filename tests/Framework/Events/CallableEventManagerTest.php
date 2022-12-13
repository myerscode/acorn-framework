<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableEventManager;
use Myerscode\Acorn\Testing\Interactions\InteractsWithDispatcher;
use Tests\BaseTestCase;

class CallableEventManagerTest extends BaseTestCase
{
    use InteractsWithDispatcher;

    public function testCanClearEvents(): void
    {
        $this->dispatcher()->addListener('my-special-event', function (): void {
        });

        $this->assertNotCount(0, CallableEventManager::listeners());

        CallableEventManager::clear();

        $this->assertCount(0, CallableEventManager::listeners());
    }

    public function testCanGetRegisteredListeners(): void
    {
        CallableEventManager::clear();

        $this->assertCount(0, CallableEventManager::listeners());

        $this->dispatcher()->addListener('my-special-event', function (): void {
        });

        $this->assertCount(1, CallableEventManager::listeners());
    }

    public function testFindCallable(): void
    {
        $dispatcher = $this->dispatcher();
        $callable = function (): void {
        };
        $dispatcher->addListener('my-special-event', $callable);
        $this->assertEquals($callable, CallableEventManager::findByCallable($callable)->getCallable());
    }
}
