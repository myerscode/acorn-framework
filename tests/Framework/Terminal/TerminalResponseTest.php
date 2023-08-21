<?php

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\OutputLine;
use Myerscode\Acorn\Framework\Terminal\Process;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Myerscode\Acorn\Framework\Terminal\TerminalResponse;
use Myerscode\Acorn\Framework\Terminal\TerminalResponseInterface;
use Tests\BaseTestCase;
use Tests\Support\InteractsWithProcess;

class TerminalResponseTest extends BaseTestCase
{
    use InteractsWithProcess;

    public function testAttempts()
    {
        $response = $this->response($this->mockProcess(), 7);

        $this->assertEquals(7, $response->attempt());
    }

    public function testError()
    {
        $response = $this->response($this->mockProcess());

        $this->assertNull($response->error());
    }

    public function testExitCode()
    {
        $process = $this->mockedSuccessfulProcess();

        $response = $this->response($process);

        $this->assertEquals(0, $response->exitCode());

        $process = $this->mockedFailedProcess();

        $response = $this->response($process);

        $this->assertEquals(1, $response->exitCode());
    }

    public function testFailed()
    {
        $process = $this->mockedFailedProcess();

        $response = $this->response($process);

        $this->assertTrue($response->failed());
        $this->assertFalse($response->successful());
    }

    public function testOutput()
    {
        $console = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $response = $console->run('echo Hello Acorn', $voidOutput);

        $this->assertEquals('Hello Acorn', $response->output());
    }

    public function testSleptFor()
    {
        $response = $this->response($this->mockProcess(), 7, 490);

        $this->assertEquals(490, $response->sleptFor());
    }

    public function testSuccessful()
    {
        $process = $this->mockedSuccessfulProcess();

        $response = $this->response($process);

        $this->assertTrue($response->successful());
        $this->assertFalse($response->failed());
    }

    public function testThrowIfFailed()
    {
        $process = $this->mockedFailedProcess(function ($mock) {
            $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
        });

        $response = $this->response($process);

        $this->expectException(ProcessFailedException::class);

        $response->throw();
    }

    public function testThrowIfNotFailed()
    {
        $process = $this->mockedSuccessfulProcess();

        $response = $this->response($process);

        $this->assertNull($response->throw());
    }

    private function response(Process $process, int $attempt = 1, int $sleptFor = 0): TerminalResponseInterface
    {
        return new class ($process, $attempt, $sleptFor) extends TerminalResponse {
        };
    }
}
