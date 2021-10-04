<?php

namespace Tests\Framework;

use Exception;
use Myerscode\Acorn\Application;
use Myerscode\Acorn\Foundation\Console\ConfigInput;
use Myerscode\Acorn\Foundation\Console\VoidOutput;
use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Log\LogInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\CommandThatErrorsCommand;

class ApplicationTest extends BaseTestCase
{

    public function testIsBuildable()
    {
        $app = new Application($this->container(), new Dispatcher);

        $this->assertEquals(Application::APP_NAME, $app->getName());
        $this->assertEquals(Application::APP_VERSION, $app->getVersion());
    }

    public function testEventsAreLoaded()
    {
        $dispatcher = new Dispatcher;
        $app = new Application($this->container(), $dispatcher);

        $this->assertCount(3, $dispatcher->getListeners());
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandAfterEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandBeforeEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandErrorEvent::class));
    }

    public function testEmitsError()
    {
        $dispatcher = \Mockery::spy(Dispatcher::class);
        $app = new Application($this->container(), $dispatcher);
        $app->add(new CommandThatErrorsCommand());

        $result = $this->catch(Exception::class)->from(function () use ($app) {
            return $app->run(new ConfigInput(['error-command']), new VoidOutput());
        });

        $dispatcher->shouldHaveReceived('emit')->with(CommandErrorEvent::class, ConsoleErrorEvent::class)->once();

        $this->assertEquals(true, $result->failed());
    }

    public function testHasInstanceOfLogger()
    {
        $app = new Application($this->container(), new Dispatcher);
        $this->assertInstanceOf(LogInterface::class, $app->logger());
    }

}
