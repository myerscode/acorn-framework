<?php

declare(strict_types=1);

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Tester\CommandTester;
use Tests\BaseTestCase;

final class InteractsWithProgressTest extends BaseTestCase
{
    public function testCreateProgressBar(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:progress-create';

            public function handle(): int
            {
                $bar = $this->createProgressBar(10);
                $bar->start();

                for ($i = 0; $i < 10; $i++) {
                    $bar->advance();
                }

                $bar->finish();
                $this->output->writeln('');
                $this->line('done');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertStringContainsString('done', $tester->getDisplay());
    }

    public function testCreateProgressBarReturnsProgressBarInstance(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:progress-instance';

            public ?ProgressBar $capturedBar = null;

            public function handle(): int
            {
                $this->capturedBar = $this->createProgressBar(5);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertInstanceOf(ProgressBar::class, $command->capturedBar);
        $this->assertSame(5, $command->capturedBar->getMaxSteps());
    }

    public function testWithProgressBarHandlesEmptyCollection(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:progress-empty';

            public bool $callbackCalled = false;

            public function handle(): int
            {
                $this->withProgressBar([], function (mixed $item): void {
                    $this->callbackCalled = true;
                });

                $this->line('finished');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertFalse($command->callbackCalled);
        $this->assertStringContainsString('finished', $tester->getDisplay());
    }

    public function testWithProgressBarIteratesItems(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:progress-items';

            /** @var string[] */
            public array $processed = [];

            public function handle(): int
            {
                $this->withProgressBar(['alpha', 'beta', 'gamma'], function (string $item): void {
                    $this->processed[] = $item;
                });

                $this->line('complete');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(['alpha', 'beta', 'gamma'], $command->processed);
        $this->assertStringContainsString('complete', $tester->getDisplay());
    }

    public function testWithSpinnerExecutesCallback(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:spinner';

            public function handle(): int
            {
                $result = $this->withSpinner(fn (): string => 'computed-value', 'Working...');
                $this->line('Result: ' . $result);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $display = $tester->getDisplay();
        $this->assertStringContainsString('Working...', $display);
        $this->assertStringContainsString('done.', $display);
        $this->assertStringContainsString('Result: computed-value', $display);
    }

    public function testWithSpinnerReturnsCallbackResult(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:spinner-return';

            public mixed $result = null;

            public function handle(): int
            {
                $this->result = $this->withSpinner(fn (): int => 42);

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame(42, $command->result);
    }

    public function testWithTaskReturnsCallbackResult(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:task-return';

            public mixed $result = null;

            public function handle(): int
            {
                $this->result = $this->withTask('Computing...', fn (): string => 'task-result');

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertSame('task-result', $command->result);
    }

    public function testWithTaskShowsElapsedTime(): void
    {
        $command = new class () extends Command {
            protected string $signature = 'test:task';

            public function handle(): int
            {
                $this->withTask('Doing something...', function (): void {
                    // simulate quick task
                });

                return 0;
            }
        };

        $tester = new CommandTester($command);
        $tester->execute([]);

        $display = $tester->getDisplay();
        $this->assertStringContainsString('Doing something...', $display);
        $this->assertMatchesRegularExpression('/\(\d+\.?\d*s\)/', $display);
    }
}
