<?php

declare(strict_types=1);

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\InteractsWithSignals;
use Myerscode\Acorn\Framework\Exceptions\CommandFailedException;
use Myerscode\Acorn\Testing\Interactions\InteractsWithCommands;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\SimpleOutputCommand;

final class CommandFeaturesTest extends BaseTestCase
{
    use InteractsWithCommands;

    public function testCallSilentlySuppressesSubCommandOutput(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'quiet-parent';

            public function handle(): int
            {
                // SimpleOutputCommand writes its name; callSilently swallows it.
                $this->callSilently(SimpleOutputCommand::class, ['name' => 'Gerald']);
                $this->output->writeln('parent done');

                return 0;
            }
        };

        $output = $this->call($command);

        $this->assertStringContainsString('parent done', $output);
        $this->assertStringNotContainsString('Gerald', $output);
    }

    public function testConstructorInjectionWorksForDiscoveredCommands(): void
    {
        // A command that declares a dependency in its constructor is autowired
        // when the container builds it (as discovery does via container->get).
        $command = $this->application()->container()->get(\Tests\Resources\App\Commands\ServiceBackedCommand::class);

        $output = $this->call($command);

        $this->assertStringContainsString('Rupert', $output);
    }

    public function testFailThrowsWithChosenExitCode(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'boom';

            public function handle(): int
            {
                $this->fail('Rupert chewed the cable', 42);
            }
        };

        try {
            $this->call($command);
            $this->fail('Expected CommandFailedException');
        } catch (CommandFailedException $commandFailedException) {
            $this->assertSame('Rupert chewed the cable', $commandFailedException->getMessage());
            $this->assertSame(42, $commandFailedException->exitCode());
        }
    }

    public function testResolvePullsADependencyFromTheContainer(): void
    {
        $command = new class () extends Command {
            public ?object $injected = null;

            protected string $signature = 'resolve-me';

            public function handle(): int
            {
                $service = $this->resolve(\Tests\Resources\CorgiService::class);
                $this->injected = $service;
                $this->output->writeln($service->name());

                return 0;
            }
        };

        $output = $this->call($command);

        $this->assertInstanceOf(\Tests\Resources\CorgiService::class, $command->injected);
        $this->assertStringContainsString('Rupert', $output);
    }

    public function testSignalHandlerDefaultsToTerminateWhenNoneRegistered(): void
    {
        $command = new class () extends Command implements SignalableCommandInterface {
            use InteractsWithSignals;

            protected string $signature = 'no-handlers';

            public function handle(): int
            {
                return 0;
            }
        };

        $this->assertSame([], $command->getSubscribedSignals());
        $this->assertSame(0, $command->handleSignal(SIGTERM));
    }

    public function testSignalHandlerRegistrationAndDispatch(): void
    {
        $command = new class () extends Command implements SignalableCommandInterface {
            use InteractsWithSignals;

            public bool $handled = false;

            protected string $signature = 'signalling';

            public function handle(): int
            {
                $this->onSignal(SIGINT, function (): int {
                    $this->handled = true;

                    return 0;
                });

                return 0;
            }
        };

        $this->call($command);

        // The command subscribes to the signals it registered.
        $this->assertSame([SIGINT], $command->getSubscribedSignals());

        // Dispatching the signal runs the handler and returns its exit code.
        $this->assertSame(0, $command->handleSignal(SIGINT));
        $this->assertTrue($command->handled);
    }
}
