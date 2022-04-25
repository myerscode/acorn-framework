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
use Myerscode\Acorn\Testing\Interactions\InteractsWithContainer;
use Myerscode\Acorn\Testing\Interactions\InteractsWithDispatcher;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\CommandThatErrorsCommand;

use function Myerscode\Acorn\Foundation\container;

class ApplicationTest extends BaseTestCase
{
    use InteractsWithContainer;
    use InteractsWithDispatcher;

    public function testIsBuildable(): void
    {
        $application = new Application($this->container());

        $this->assertEquals(Application::APP_NAME, $application->getName());
        $this->assertEquals(Application::APP_VERSION, $application->getVersion());
    }

    public function testEventsAreLoaded(): void
    {
        $app = new Application($this->newContainer());

        $dispatcher = $app->dispatcher();

        $this->assertGreaterThanOrEqual(3, $dispatcher->getListeners());
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandAfterEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandBeforeEvent::class));
        $this->assertCount(1, $dispatcher->getListenersForEvent(CommandErrorEvent::class));
    }

    public function testCanHandleInvalidEventsDirectory(): void
    {
        $dispatcher = $this->dispatcher();

        new class($this->container()) extends Application {
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
        $dispatcher = $this->spy(Dispatcher::class, [new SynchronousQueue()]);

        $application = new Application($this->container());

        $application->add(new CommandThatErrorsCommand());

        container()->swap(Dispatcher::class, $dispatcher);

        $result = $this->catch(Exception::class)->from(fn() => $application->handle(new ConfigInput(['error-command']), new VoidOutput()));

        $dispatcher->shouldHaveReceived('emit')->with(CommandErrorEvent::class, ConsoleErrorEvent::class)->once();

        $this->assertEquals(true, $result->failed());
    }

    public function testCommandsAreLoaded(): void
    {
        $application = new Application($this->container());
        $this->assertGreaterThanOrEqual(3, is_countable($application->all()) ? count($application->all()) : 0);
    }

    public function testCanHandleInvalidCommandsDirectory(): void
    {
        $dispatcher = $this->dispatcher();
        $container = $this->container();

        new class($container) extends Application {
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
        $application = new Application($this->container());
        $this->assertInstanceOf(LogInterface::class, $application->logger());
    }

}
