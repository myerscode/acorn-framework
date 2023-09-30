<?php

namespace Tests\Foundation;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Console\Input\Input;
use Myerscode\Acorn\Framework\Container\Container;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Myerscode\Acorn\Testing\Interactions\InteractsWithContainer;
use Myerscode\Acorn\Testing\Interactions\InteractsWithDispatcher;
use Myerscode\Utilities\Bags\Utility as BagUtility;
use Tests\BaseTestCase;
use Tests\Resources\CountingListener;
use Tests\Resources\TestEvent;

use function Myerscode\Acorn\Foundation\bag;
use function Myerscode\Acorn\Foundation\config;
use function Myerscode\Acorn\Foundation\container;
use function Myerscode\Acorn\Foundation\dispatch;
use function Myerscode\Acorn\Foundation\emit;
use function Myerscode\Acorn\Foundation\input;
use function Myerscode\Acorn\Foundation\output;
use function Myerscode\Acorn\Foundation\terminal;

class HelpersTest extends BaseTestCase
{
    use InteractsWithContainer;
    use InteractsWithDispatcher;

    public function testBagHelper(): void
    {
        $this->assertInstanceOf(BagUtility::class, bag());
    }

    public function testConfigHelper(): void
    {
        $this->container()->get('config')->store()->set('corgis', ['long' => 'Gerald', 'short' => 'Rupert']);

        $this->assertIsArray(config());
        $this->assertSame('Gerald', config('corgis.long'));
        $this->assertSame('Rupert', config('corgis.short'));
        $this->assertSame(['long' => 'Gerald', 'short' => 'Rupert'], config('corgis'));
        $this->assertSame('Audrey', config('rabbit', 'Audrey'));
    }

    public function testContainerHelper(): void
    {
        $this->assertInstanceOf(Input::class, container('input'));
        $this->assertInstanceOf(Container::class, container());
    }

    public function testDispatchHelper(): void
    {
        $dispatcher = $this->dispatcher();
        $countingListener = new CountingListener();
        $dispatcher->addListener(TestEvent::class, $countingListener);

        container()->swap(Dispatcher::class, $dispatcher);

        dispatch(new TestEvent());

        $this->assertSame(1, $countingListener->counter());
    }

    public function testEmitHelper(): void
    {
        $dispatcher = $this->dispatcher();
        $countingListener = new CountingListener();
        $dispatcher->addListener(TestEvent::class, $countingListener);

        container()->swap(Dispatcher::class, $dispatcher);

        $counter = 0;

        $dispatcher->addListener('test.event', static function () use (&$counter) : void {
            $counter = 7749;
        });

        emit('test.event');

        $this->assertSame(7749, $counter);
    }

    public function testInputHelper(): void
    {
        $this->assertInstanceOf(Input::class, input());
    }

    public function testOutputHelper(): void
    {
        $this->assertInstanceOf(DisplayOutput::class, output());
    }

    public function testTerminalHelper(): void
    {
        $this->assertInstanceOf(Terminal::class, terminal());
    }
}
