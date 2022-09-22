<?php

namespace Tests\Framework;

use Myerscode\Acorn\Foundation\Console\ConfigInput;
use Myerscode\Acorn\Foundation\Console\Input;
use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Foundation\Console\VoidOutput;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Myerscode\Acorn\Kernel;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\CommandThatErrorsCommand;

class KernelTest extends BaseTestCase
{

    public function testCanRun(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,configManager]', [$this->resourceFilePath('/Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(),
                'output' => new VoidOutput(),
                'configManager' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(0, $kernel->run());
    }

    public function testHandlesErrors(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,configManager]', [$this->resourceFilePath('/Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['error-command']),
                'output' => new VoidOutput(),
                'configManager' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testHandlesMissingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,configManager]', [$this->resourceFilePath('/Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['unknown-command']),
                'output' => new VoidOutput(),
                'configManager' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testCanMakeInput(): void
    {
        $kernel = $this->mock(Kernel::class.'[configManager]', [$this->resourceFilePath('/Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'configManager' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertInstanceOf(ConsoleInputInterface::class, $kernel->input());
    }

    public function testCanMakeOutput(): void
    {
        $kernel = $this->mock(Kernel::class.'[configManager]', [$this->resourceFilePath('/Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'configManager' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertInstanceOf(ConsoleOutputInterface::class, $kernel->output());
    }
}
