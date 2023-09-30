<?php

namespace Tests\Framework\Console;

use Myerscode\Acorn\Foundation\Console\Input\ConfigInput;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Testing\Interactions\InteractsWithCommands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\SimpleOutputCommand;

class CommandTest extends BaseTestCase
{
    use InteractsWithCommands;

    public function makeTestCommand(): Command
    {
        return new class extends Command {
            protected function configure(): void
            {
                $this
                    ->addArgument('argument-a', InputArgument::REQUIRED, 'first argument')
                    ->addArgument('argument-b', InputArgument::OPTIONAL, 'second argument', 'a-default-argument-value')
                    ->addArgument('argument-c', InputArgument::OPTIONAL, 'third argument');

                $this
                    ->addOption('option-a', 'a', InputOption::VALUE_OPTIONAL, 'first option')
                    ->addOption('option-b', 'b', InputOption::VALUE_REQUIRED, 'second option', 'a-default-option-value')
                    ->addOption('option-c', 'c', InputOption::VALUE_REQUIRED, 'third option');
            }

            public function handle(): void
            {
                //
            }
        };
    }

    public function testCanGetAllCommandArguments(): void
    {
        $command = $this->makeTestCommand();

        $configInput = new ConfigInput([
            'argument-a' => 'test-first-argument',
        ]);

        $voidOutput = $this->createVoidOutput();

        $command->run($configInput, $voidOutput);

        $this->assertSame([
            'argument-a' => 'test-first-argument',
            'argument-b' => 'a-default-argument-value',
            'argument-c' => null,
        ], $command->arguments());
    }

    public function testCanGetAllCommandOptions(): void
    {
        $command = $this->makeTestCommand();

        $configInput = new ConfigInput([
            'argument-a' => 'test-first-argument',
            '--option-a' => 'test-first-option',
        ]);

        $voidOutput = $this->createVoidOutput();

        $command->run($configInput, $voidOutput);

        $this->assertSame([
            'option-a' => 'test-first-option',
            'option-b' => 'a-default-option-value',
            'option-c' => null,
        ], $command->options());
    }

    public function testCanGetArgumentWithDefaultValue(): void
    {
        $command = $this->makeTestCommand();

        $configInput = new ConfigInput([
            'argument-a' => 'test-first-argument',
        ]);

        $voidOutput = $this->createVoidOutput();

        $command->run($configInput, $voidOutput);

        $this->assertSame('test-first-argument', $command->argument('argument-a'));
        $this->assertSame('a-default-argument-value', $command->argument('argument-b'));
        $this->assertSame('custom-value-if-null', $command->argument('argument-c', 'custom-value-if-null'));
    }

    public function testCanGetOptionWithDefaultValue(): void
    {
        $command = $this->makeTestCommand();

        $configInput = new ConfigInput([
            'argument-a' => 'test-first-argument',
            '--option-a' => 'test-first-option',
        ]);

        $voidOutput = $this->createVoidOutput();

        $command->run($configInput, $voidOutput);

        $this->assertSame('test-first-option', $command->option('option-a'));
        $this->assertSame('a-default-option-value', $command->option('option-b'));
        $this->assertSame('custom-option-if-null', $command->option('option-c', 'custom-option-if-null'));
    }

    public function testCommandCanCallAnotherCommand(): void
    {
        $command = new class extends Command {
            protected string $signature = 'anon {name : The name to pass through}';

            public function handle(): void
            {
                $this->call(SimpleOutputCommand::class, ['name' => $this->argument('name')]);
            }
        };

        $display = $this->call($command, ['name' => 'Gerald']);

        $this->assertSame('Hello Gerald', $display);

        $display = $this->call(SimpleOutputCommand::class, ['name' => 'Rupert']);

        $this->assertSame('Hello Rupert', $display);
    }
}
