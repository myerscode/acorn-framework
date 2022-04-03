<?php

namespace Tests\Foundation;

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Foundation\Console\Input;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Tests\BaseTestCase;
use Tests\Resources\CountingListener;
use Tests\Resources\TestEvent;

use function Myerscode\Acorn\Foundation\config;
use function Myerscode\Acorn\Foundation\container;
use function Myerscode\Acorn\Foundation\dispatch;

class HelpersTest extends BaseTestCase
{
    public function testConfigHelper(): void
    {
        $this->container()->get('config')->store()->set('corgis', ['long' => 'Gerald', 'short' => 'Rupert']);

        $this->assertIsArray(config());
        $this->assertEquals('Gerald', config('corgis.long'));
        $this->assertEquals('Rupert', config('corgis.short'));
        $this->assertEquals(['long' => 'Gerald', 'short' => 'Rupert'], config('corgis'));
        $this->assertEquals('Audrey', config('rabbit', 'Audrey'));
    }

    public function testContainerHelper(): void
    {
        $this->assertInstanceOf(Input::class, container('input'));
        $this->assertInstanceOf(Container::class, container());
    }

    public function testDispatchHelper(): void
    {
        $dispatcher = $this->dispatcher();
        $listener = new CountingListener();
        $dispatcher->addListener(TestEvent::class, $listener);

        container()->swap(Dispatcher::class, $dispatcher);

        dispatch(new TestEvent());

        $this->assertEquals(1, $listener->counter());
    }
}
