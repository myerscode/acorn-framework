<?php

namespace Tests\Framework\Terminal;

use Mockery;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\FailedResponse;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;
use Tests\Support\InteractsWithProcess;
use Tests\Support\InteractsWithTerminal;

use function Myerscode\Acorn\Foundation\container;

class ConsoleTest extends BaseTestCase
{
    use InteractsWithProcess;
    use InteractsWithTerminal;

    public function testCanHandleExceptionInUnderlyingProcess()
    {
        $process = $this->mockedFailedProcess(function ($process) {
            $process->shouldReceive('start')->andThrow(new \Exception());
            $process->shouldReceive('run')->andThrow(new \Exception());
            $process->shouldReceive('getCommandLine')->andReturn('ls -la');
        });


        $console = Mockery::mock(Terminal::class);

        $console->makePartial()
            ->shouldReceive('process')
            ->andReturn($process);

        $console->throw();

        $this->expectException(ProcessFailedException::class);

        $console->run('ls -la');
    }

    public function testClosureCallback()
    {
        $console = (new Terminal());
        $output = '';

        $console->run('echo Hello Acorn', function ($data) use (&$output) {
            $output = $data;
        });

        $this->assertEquals('Hello Acorn', $output);
    }

    public function testCommand()
    {
        $console = (new Terminal())->command('ls -la');

        $this->assertEquals('ls -la', $console->process()->getCommandLine());
    }

    public function testDisplayCallback()
    {
        $console = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $console->run('echo Hello Acorn', $voidOutput);

        $this->assertEquals('Hello Acorn', $voidOutput->output());
    }

    public function testDontThrow()
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(function ($mock) {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
            })
        );

        $console->dontThrow();

        $response = $console->run('ls -la');

        $this->assertInstanceOf(FailedResponse::class, $response);
    }

    public function testIdleTimeout()
    {
        $console = (new Terminal())->timeoutWhenOutputIsIdle(10);

        $this->assertEquals(10, $console->process()->getIdleTimeout());

        $console->dontTimeoutWhenOutputIsIdle();

        $this->assertEquals(null, $console->process()->getIdleTimeout());
    }

    public function testIn()
    {
        $console = (new Terminal())->in('my/directory');

        $this->assertEquals('my/directory', $console->process()->getWorkingDirectory());
    }

    public function testInBackground()
    {
        $console = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $console->run('echo Hello Acorn', $voidOutput);

        $console->async('echo Hello Acorn');
        $this->assertTrue($console->inBackground());

        $console->block()->run('echo Hello Acorn');
        $this->assertFalse($console->inBackground());
    }

    public function testPassesNewEnvironmentVariables()
    {
        $console = (new Terminal());

        $voidOutput = $this->createStreamOutput();

        $console->run('echo Hello $APP_NAME', $voidOutput);

        $this->assertEquals('Hello Acorn', $voidOutput->output());

        $voidOutput = $this->createStreamOutput();

        $console->withEnvironmentVariables(['MY_NAME' => 'Fred'])->run('echo Hello $MY_NAME', $voidOutput);

        $this->assertEquals('Hello Fred', $voidOutput->output());
    }

    public function testRetries()
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(function ($mock) {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
                $mock->shouldReceive('isSuccessful')
                    ->times(4)
                    ->andReturn(false, false, true, true);
            })
        );

        $response = $this->catch(ProcessFailedException::class)->from(function () use ($console) {
            return $console->retries(3)->run('ls -la');
        });

        $this->assertEquals(3, $console->attempts());
        $this->assertTrue($response->successful());
    }

    public function testSetTty()
    {
        $console = (new Terminal())->enableTty();

        if ($console->process()->isTtySupported()) {
            $this->assertTrue($console->process()->isTty());
        } else {
            $this->assertFalse($console->process()->isTty());
        }

        $console = (new Terminal())->disableTty();
        if ($console->process()->isTtySupported()) {
            $this->assertFalse($console->process()->isTty());
        } else {
            $this->assertFalse($console->process()->isTty());
        }
    }

    public function testSleep()
    {
        $console = (new Terminal())->sleep(5);

        $this->assertEquals(5, $console->sleepsFor());
    }

    public function testSleepsBetweenRetries()
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(function ($mock) {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
                $mock->shouldReceive('isSuccessful')
                    ->times(3)
                    ->andReturn(false, false, true);
            })
        );

        $response = $console->retries(3)->sleep(5)->run('ls -la');

        $this->assertEquals(10, $response->sleptFor());
    }

    public function testThrowsExceptionIfCommandFails()
    {
        $console = $this->mockedTerminalWithProcess(
            $this->mockedFailedProcess(function ($mock) {
                $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
            })
        );

        $console->throw();

        $this->expectException(ProcessFailedException::class);

        $console->run('ls -la');
    }

    public function testTimeout()
    {
        $console = (new Terminal())->timeout(20);

        $this->assertEquals(20, $console->process()->getTimeout());
    }

    public function testTtyIsDisabled()
    {
        $output = $this->createStreamOutput();

        container()->swap(DisplayOutput::class, $output);
        container()->swap('output', $output);
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $console = $this->mockedTerminal(function ($mock) {
            $mock->shouldReceive('isTtySupported')->andReturn(false);
        });

        $console->enableTty();
        $console->run('echo ');

        $this->assertEquals("[WARNING] Tried setting tty on Terminal command - but it is not supported!", $output->output());
    }
}
