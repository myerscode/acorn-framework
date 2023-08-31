<?php

namespace Tests\Support;

use Mockery;
use Myerscode\Acorn\Framework\Terminal\Process;
use Myerscode\Acorn\Framework\Terminal\Terminal;

trait InteractsWithTerminal
{

    public function mockedTerminal(callable $mockTerminalCallback = null): Terminal
    {
        $mockedTerminal = Mockery::mock(Terminal::class);

        $mockedTerminal->makePartial();

        if (!is_null($mockTerminalCallback)) {
            $mockTerminalCallback($mockedTerminal);
        }

        return $mockedTerminal;
    }

    public function mockedTerminalWithProcess(Process $process): Terminal
    {
        $mockedTerminal = $this->mockedTerminal();

        $mockedTerminal->makePartial()
            ->shouldReceive('process')
            ->andReturn($process);

        return $mockedTerminal;
    }
}
