<?php

namespace Tests\Framework;

use Exception;
use Myerscode\Acorn\Application;
use Myerscode\Acorn\Foundation\Console\ConfigInput;
use Myerscode\Acorn\Foundation\Console\VoidOutput;
use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Log\LogInterface;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\CommandThatErrorsCommand;

class ApplicationTest extends BaseTestCase
{

    public function testIsBuildable(): void
    {
        $application = null;
        $application = new Application($this->container(), $this->dispatcher());

        $this->assertEquals(Application::APP_NAME, $application->getName());
        $this->assertEquals(Application::APP_VERSION, $application->getVersion());
    }

    public function testEventsAreLoaded(): void
    {
        $dispatcher = $this->dispatcher();
        $container = $this->container();

        new Application($container, $dispatcher);

        $this->assertGreaterThanOrEqual(3, $dispatcher->getListeners());
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandAfterEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandBeforeEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandErrorEvent::class));
    }

    public function testCanHandleInvalidEventsDirectory(): void
    {
        $dispatcher = $this->dispatcher();
        $container = $this->container();

        new class($container, $dispatcher) extends Application {
            public function eventDiscoveryDirectories(): array
            {
                return [
                    'invalid-path',
                ];
            }
        };

        $this->assertGreaterThanOrEqual(0, $dispatcher->getListeners());
    }

    public function testEmitsError(): void
    {
        $application = null;
        $dispatcher = $this->spy(Dispatcher::class, [new SynchronousQueue()]);
        $application = new Application($this->container(), $dispatcher);
        $application->add(new CommandThatErrorsCommand());

        $result = $this->catch(Exception::class)->from(fn() => $application->handle(new ConfigInput(['error-command']), new VoidOutput()));

        $dispatcher->shouldHaveReceived('emit')->with(CommandErrorEvent::class, ConsoleErrorEvent::class)->once();

        $this->assertEquals(true, $result->failed());
    }

    public function testCommandsAreLoaded(): void
    {
        $application = null;
        $application = new Application($this->container(),  $this->dispatcher());
        $this->assertGreaterThanOrEqual(3, is_countable($application->all()) ? count($application->all()) : 0);
    }

    public function testCanHandleInvalidCommandsDirectory(): void
    {
        $dispatcher = $this->dispatcher();
        $container = $this->container();

        new class($container, $dispatcher) extends Application {
            public function commandsDiscoveryDirectories(): array
            {
                return [
                    'invalid-path',
                ];
            }
        };

        $this->assertGreaterThanOrEqual(0, $dispatcher->getListeners());
    }

    public function testHasInstanceOfLogger(): void
    {
        $application = null;
        $application = new Application($this->container(), $this->dispatcher());
        $this->assertInstanceOf(LogInterface::class, $application->logger());
    }

}
