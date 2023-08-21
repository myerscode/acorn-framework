<?php

namespace Tests\Support;

use Mockery;
use Myerscode\Acorn\Framework\Terminal\Process;

trait InteractsWithProcess
{
    public function mockProcess(callable $mockProcessCallback = null): Process
    {
        $process = Mockery::mock(Process::class);

        $process->makePartial();

        if (!is_null($mockProcessCallback)) {
            $mockProcessCallback($process);
        }

        return $process;
    }

    public function mockedFailedProcess(callable $mockProcessCallback = null): Process
    {
        $process = $this->mockProcess($mockProcessCallback);

        $process->shouldReceive('getOutput')->andReturn('getOutput > Failed Mocked Process');
        $process->shouldReceive('getErrorOutput')->andReturn('getErrorOutput > Failed Mocked Process');
        $process->shouldReceive('getExitCode')->andReturn(1);

        $process->shouldReceive('run')->andReturn(1);
        $process->shouldReceive('start')->andReturn(1);

        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getExitCode')->andReturn(1);

        return $process;
    }

    public function mockedSuccessfulProcess(callable $mockProcessCallback = null): Process
    {
        $process = $this->mockProcess($mockProcessCallback);

        $process->shouldReceive('run')->andReturn(0);
        $process->shouldReceive('start')->andReturn(0);

        $process->shouldReceive('isSuccessful')->andReturn(true);

        $process->shouldReceive('getExitCode')->andReturn(0);

        return $process;
    }
}
