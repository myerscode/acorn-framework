<?php

namespace Tests\Support;

use Mockery;
use Myerscode\Acorn\Framework\Terminal\Process;
use Myerscode\Acorn\Framework\Terminal\Terminal;

trait InteractsWithTerminal
{

    public function mockedTerminal(callable $mockProcessCallback = null): Terminal
    {
//        $process = Mockery::mock(Process::class);
//
//        $process->shouldReceive('run')->andReturn(0);
//        $process->shouldReceive('start')->andReturn(0);
//
//        if (!is_null($mockProcessCallback)) {
//            $mockProcessCallback($process);
//        }

        $mockedTerminal = Mockery::mock(Terminal::class);

        $mockedTerminal->makePartial();

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
