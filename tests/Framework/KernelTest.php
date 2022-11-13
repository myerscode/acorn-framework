<?php

namespace Tests\Framework;

use Myerscode\Acorn\Foundation\Console\ConfigInput;
use Myerscode\Acorn\Framework\Config\Manager as ConfigManager;
use Myerscode\Acorn\Foundation\Console\VoidOutput;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Kernel;
use Tests\BaseTestCase;
use Exception;

class KernelTest extends BaseTestCase
{

    public function testCanRun(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(),
                'output' => new VoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(0, $kernel->run());
    }

    public function testCanHandleFailingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[output,processCommand]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'processCommand' => new Result(1, new Exception("Testing errors are handled")),
                'output' => new VoidOutput(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testHandlesErrors(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['error-command']),
                'output' => new VoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testHandlesMissingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['unknown-command']),
                'output' => new VoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testCanGetInput(): void
    {
        $kernel = $this->mock(Kernel::class.'[config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertInstanceOf(ConsoleInputInterface::class, $kernel->input());
    }

    public function testCanGetOutput(): void
    {
        $kernel = $this->mock(Kernel::class.'[config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertInstanceOf(ConsoleOutputInterface::class, $kernel->output());
    }

    public function testCanGetConfig(): void
    {
        $kernel = $this->mock(Kernel::class, [$this->resourceFilePath('Resources/App')])->makePartial();

        $this->assertInstanceOf(ConfigManager::class, $kernel->config());
    }
}
