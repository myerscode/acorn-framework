<?php

namespace Tests\Framework\Terminal;

use Exception;
use Mockery;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\FailedResponse;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Myerscode\Acorn\Framework\Terminal\TerminalResponse;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;
use Tests\Support\InteractsWithProcess;
use Tests\Support\InteractsWithTerminal;

use function Myerscode\Acorn\Foundation\container;

class ConsoleTest extends BaseTestCase
{
    use InteractsWithProcess;
    use InteractsWithTerminal;

    public function testCanHandleExceptionInUnderlyingProcess(): void
    {
        $process = $this->mockedFailedProcess(static function ($process): void {
            $process->shouldReceive('start')->andThrow(new Exception());
            $process->shouldReceive('run')->andThrow(new Exception());
            $process->shouldReceive('getCommandLine')->andReturn('ls -la');
        });


        $legacyMock = Mockery::mock(Terminal::class);

        $legacyMock->makePartial()
            ->shouldReceive('process')
            ->andReturn($process);

        $legacyMock->throw();

        $this->expectException(ProcessFailedException::class);

        $legacyMock->run('ls -la');
    }

    public function testClosureCallback(): void
    {
        $terminal = (new Terminal());
        $output = '';

        $terminal->run('echo Hello Acorn', static function ($data) use (&$output): void {
            $output = $data;
        });

        $this->assertSame('Hello Acorn', $output);
    }

    public function testCommand(): void
    {
        $terminal = (new Terminal())->command('ls -la');

        $this->assertSame('ls -la', $terminal->process()->getCommandLine());
    }

    public function testDisplayCallback(): void
    {
        $terminal = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $terminal->run('echo Hello Acorn', $voidOutput);

        $this->assertSame('Hello Acorn', $voidOutput->output());
    }

    public function testDontThrow(): void
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(static function ($mock): void {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
            })
        );

        $console->dontThrow();

        $response = $console->run('ls -la');

        $this->assertInstanceOf(FailedResponse::class, $response);
    }

    public function testIdleTimeout(): void
    {
        $terminal = (new Terminal())->timeoutWhenOutputIsIdle(10);

        $this->assertSame(10.0, $terminal->process()->getIdleTimeout());

        $terminal->dontTimeoutWhenOutputIsIdle();

        $this->assertEquals(null, $terminal->process()->getIdleTimeout());
    }

    public function testIn(): void
    {
        $terminal = (new Terminal())->in('my/directory');

        $this->assertSame('my/directory', $terminal->process()->getWorkingDirectory());
    }

    public function testInBackground(): void
    {
        $terminal = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $terminal->run('echo Hello Acorn', $voidOutput);

        $terminal->async('echo Hello Acorn');
        $this->assertTrue($terminal->inBackground());

        $terminal->block()->run('echo Hello Acorn');
        $this->assertFalse($terminal->inBackground());
    }

    public function testPassesNewEnvironmentVariables(): void
    {
        $terminal = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $terminal->run('echo Hello $APP_NAME', $voidOutput);

        $this->assertSame('Hello Acorn', $voidOutput->output());

        $voidOutput = $this->createStreamOutput();

        $terminal->withEnvironmentVariables(['MY_NAME' => 'Fred'])->run('echo Hello $MY_NAME', $voidOutput);

        $this->assertSame('Hello Fred', $voidOutput->output());
    }

    public function testRetries(): void
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(static function ($mock): void {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
                $mock->shouldReceive('isSuccessful')
                    ->times(4)
                    ->andReturn(false, false, true, true);
            })
        );

        $response = $this->catch(ProcessFailedException::class)->from(static fn(): TerminalResponse => $console->retries(3)->run('ls -la'));

        $this->assertSame(3, $console->attempts());
        $this->assertTrue($response->successful());
    }

    public function testSetTty(): void
    {
        $console = (new Terminal())->enableTty();

        if ($console->process()->isTtySupported()) {
            $this->assertTrue($console->process()->isTty());
        } else {
            $this->assertFalse($console->process()->isTty());
        }

        $console = (new Terminal())->disableTty();
        $this->assertFalse($console->process()->isTty());
    }

    public function testSleep(): void
    {
        $terminal = (new Terminal())->sleep(5);

        $this->assertSame(5, $terminal->sleepsFor());
    }

    public function testSleepsBetweenRetries(): void
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(static function ($mock): void {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
                $mock->shouldReceive('isSuccessful')
                    ->times(3)
                    ->andReturn(false, false, true);
            })
        );

        $terminalResponse = $console->retries(3)->sleep(5)->run('ls -la');

        $this->assertSame(10, $terminalResponse->sleptFor());
    }

    public function testThrowsExceptionIfCommandFails(): void
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(static function ($mock): void {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
            })
        );

        $console->throw();

        $this->expectException(ProcessFailedException::class);

        $console->run('ls -la');
    }

    public function testTimeout(): void
    {
        $terminal = (new Terminal())->timeout(20);

        $this->assertSame(20.0, $terminal->process()->getTimeout());
    }

    public function testTtyIsDisabled(): void
    {
        $output = $this->createStreamOutput();

        container()->swap(DisplayOutput::class, $output);
        container()->swap('output', $output);

        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $terminal = $this->mockedTerminal(static function ($mock): void {
            $mock->shouldReceive('isTtySupported')->andReturn(false);
        });

        $terminal->enableTty();
        $terminal->run('echo ');

        $this->assertSame("[WARNING] Tried setting tty on Terminal command - but it is not supported!", $output->output());
    }
}
