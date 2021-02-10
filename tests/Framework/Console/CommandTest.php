<?php

namespace Tests\Framework\Console;

use League\Container\Container as DependencyManager;
use Myerscode\Acorn\Container;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Log\NullLogger;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Tests\BaseTestCase;
use Tests\Resources\TestCommand;

class CommandTest extends BaseTestCase
{

    public function makeTestCommand(): Command
    {
        return new class extends Command {

            protected function configure()
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

    public function testCanGetAllCommandArguments()
    {
        $command = $this->makeTestCommand();

        $input = new ArrayInput([
            'argument-a' => 'test-first-argument',
        ]);

        $output = new NullOutput();

        $command->run($input, $output);

        $this->assertSame([
            'argument-a' => 'test-first-argument',
            'argument-b' => 'a-default-argument-value',
            'argument-c' => null,
        ], $command->arguments());
    }

    public function testCanGetArgumentWithDefaultValue(){
        $command = $this->makeTestCommand();

        $input = new ArrayInput([
            'argument-a' => 'test-first-argument',
        ]);

        $output = new NullOutput();

        $command->run($input, $output);

        $this->assertSame('test-first-argument', $command->argument('argument-a'));
        $this->assertSame('a-default-argument-value', $command->argument('argument-b'));
        $this->assertSame('custom-value-if-null', $command->argument('argument-c', 'custom-value-if-null'));
    }

    public function testCanGetAllCommandOptions()
    {
        $command = $this->makeTestCommand();

        $input = new ArrayInput([
            'argument-a' => 'test-first-argument',
            '--option-a' => 'test-first-option',
        ]);

        $output = new NullOutput();

        $command->run($input, $output);

        $this->assertSame([
            'option-a' => 'test-first-option',
            'option-b' => 'a-default-option-value',
            'option-c' => null,
        ], $command->options());
    }

    public function testCanGetOptionWithDefaultValue()
    {
        $command = $this->makeTestCommand();

        $input = new ArrayInput([
            'argument-a' => 'test-first-argument',
            '--option-a' => 'test-first-option',
        ]);

        $output = new NullOutput();

        $command->run($input, $output);

        $this->assertSame('test-first-option', $command->option('option-a'));
        $this->assertSame('a-default-option-value', $command->option('option-b'));
        $this->assertSame('custom-option-if-null', $command->option('option-c', 'custom-option-if-null'));
    }

    public function testCommandSetsContainer()
    {
        $container = new DependencyManager;
        $command = new TestCommand();
        $command->setContainer($container);
        $this->assertSame($container, $command->getContainer());
    }
}
