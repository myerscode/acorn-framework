<?php

namespace Tests\Framework;

use Exception;
use Myerscode\Acorn\Foundation\Console\Input\ConfigInput;
use Myerscode\Acorn\Framework\Config\Manager as ConfigManager;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Kernel;
use Tests\BaseTestCase;

class KernelTest extends BaseTestCase
{

    public function testCanGetConfig(): void
    {
        $legacyMock = $this->mock(Kernel::class, [$this->resourceFilePath('Resources/App')])->makePartial();

        $this->assertInstanceOf(ConfigManager::class, $legacyMock->config());
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

        $this->assertInstanceOf(DisplayOutputInterface::class, $kernel->output());
    }

    public function testCanHandleFailingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[output,processCommand]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'processCommand' => new Result(1, new Exception("Testing errors are handled")),
                'output' => $this->createVoidOutput(),
            ])
            ->makePartial();

        $this->assertSame(1, $kernel->run());
    }

    public function testCanRun(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(),
                'output' => $this->createVoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertSame(0, $kernel->run());
    }

    public function testHandlesErrors(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['error-command']),
                'output' => $this->createVoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertSame(1, $kernel->run());
    }

    public function testHandlesMissingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output,config]', [$this->resourceFilePath('Resources/App')])
            ->shouldAllowMockingProtectedMethods()
            ->allows([
                'input' => new ConfigInput(['unknown-command']),
                'output' => $this->createVoidOutput(),
                'config' => $this->configManager(),
            ])
            ->makePartial();

        $this->assertSame(1, $kernel->run());
    }
}
