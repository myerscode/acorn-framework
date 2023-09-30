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

    public function testAttempts(): void
    {
        $response = $this->response($this->mockProcess(), 7);

        $this->assertSame(7, $response->attempt());
    }

    public function testError(): void
    {
        $response = $this->response($this->mockProcess());

        $this->assertNull($response->error());
    }

    public function testExitCode(): void
    {
        $process = $this->mockedSuccessfulProcess();

        $response = $this->response($process);

        $this->assertSame(0, $response->exitCode());

        $process = $this->mockedFailedProcess();

        $response = $this->response($process);

        $this->assertSame(1, $response->exitCode());
    }

    public function testFailed(): void
    {
        $process = $this->mockedFailedProcess();

        $response = $this->response($process);

        $this->assertTrue($response->failed());
        $this->assertFalse($response->successful());
    }

    public function testOutput(): void
    {
        $terminal = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $terminalResponse = $terminal->run('echo Hello Acorn', $voidOutput);

        $this->assertSame('Hello Acorn', $terminalResponse->output());
    }

    public function testSleptFor(): void
    {
        $response = $this->response($this->mockProcess(), 7, 490);

        $this->assertSame(490, $response->sleptFor());
    }

    public function testSuccessful(): void
    {
        $process = $this->mockedSuccessfulProcess();

        $response = $this->response($process);

        $this->assertTrue($response->successful());
        $this->assertFalse($response->failed());
    }

    public function testThrowIfFailed(): void
    {
        $process = $this->mockedFailedProcess(static function ($mock) : void {
            $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
        });

        $response = $this->response($process);

        $this->expectException(ProcessFailedException::class);

        $response->throw();
    }

    public function testThrowIfNotFailed(): void
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
