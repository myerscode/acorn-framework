<?php

namespace Tests\Framework;

use Myerscode\Acorn\Foundation\Console\ConfigInput;
use Myerscode\Acorn\Foundation\Console\VoidOutput;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Kernel;
use Tests\BaseTestCase;
use Tests\Resources\App\Commands\CommandThatErrorsCommand;

class KernelTest extends BaseTestCase
{

    public function testCanRun(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output]', [$this->resourceFilePath('/Resources/App')])
            ->allows([
                'input' => new ConfigInput(),
                'output' => new VoidOutput(),
            ])
            ->makePartial();

        $this->assertEquals(0, $kernel->run());
    }

    public function testHandlesErrors(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output]', [$this->resourceFilePath('/Resources/App')])
            ->allows([
                'input' => new ConfigInput(['error-command']),
                'output' => new VoidOutput(),
            ])
            ->makePartial();

        $kernel->application()->add(new CommandThatErrorsCommand());

        $this->assertEquals(1, $kernel->run());
    }

    public function testHandlesMissingCommand(): void
    {
        $kernel = $this->mock(Kernel::class.'[input,output]', [$this->resourceFilePath('/Resources/App')])
            ->allows([
                'input' => new ConfigInput(['unknown-command']),
                'output' => new VoidOutput(),
            ])
            ->makePartial();

        $this->assertEquals(1, $kernel->run());
    }

    public function testCanMakeInput(): void
    {
        $kernel = new Kernel($this->resourceFilePath('/Resources/App'));

        $this->assertInstanceOf(Input::class, $kernel->input());
    }

    public function testCanMakeOutput(): void
    {
        $kernel = new Kernel($this->resourceFilePath('/Resources/App'));

        $this->assertInstanceOf(Output::class, $kernel->output());
    }
}
