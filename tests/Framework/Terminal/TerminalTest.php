<?php

declare(strict_types=1);

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\FailedResponse;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Tests\BaseTestCase;

final class TerminalTest extends BaseTestCase
{
    public function testAsyncRunsInBackground(): void
    {
        $terminal = new Terminal();

        $response = $terminal->async('echo Rupert');

        $this->assertTrue($terminal->inBackground());
        // A backgrounded process is reported successful without waiting on
        // a non-zero exit; wait for it to finish before asserting output.
        $response->process()->wait();
        $this->assertStringContainsString('Rupert', $response->process()->getOutput());
    }

    public function testAttemptsResetBetweenRuns(): void
    {
        $terminal = new Terminal()->dontThrow()->retries(1);

        $terminal->run('exit 1');
        $this->assertSame(2, $terminal->attempts());

        $terminal->run('echo Arthur');
        $this->assertSame(1, $terminal->attempts());
    }

    public function testDontThrowReturnsAFailedResponseInsteadOfThrowing(): void
    {
        $response = new Terminal()->dontThrow()->run('exit 3');

        $this->assertInstanceOf(FailedResponse::class, $response);
        $this->assertFalse($response->successful());
    }

    public function testEnvironmentVariablesArePassedToTheProcess(): void
    {
        $response = new Terminal()
            ->withEnvironmentVariables(['CORGI' => 'Arthur'])
            ->runSafe(['sh', '-c', 'echo $CORGI']);

        $this->assertTrue($response->successful());
        $this->assertStringContainsString('Arthur', $response->output());
    }

    public function testRetriesAreAttemptedBeforeFailing(): void
    {
        $terminal = new Terminal()->dontThrow()->retries(2);

        $response = $terminal->run('exit 1');

        $this->assertFalse($response->successful());
        // initial attempt + 2 retries
        $this->assertSame(3, $terminal->attempts());
    }
    public function testRunExecutesACommandAndCapturesOutput(): void
    {
        $response = new Terminal()->run('echo Rupert');

        $this->assertTrue($response->successful());
        $this->assertStringContainsString('Rupert', $response->output());
    }

    public function testRunSafePassesArgumentsWithoutShellInterpretation(): void
    {
        // The whole point of runSafe(): a value containing shell
        // metacharacters is a literal argument, never interpreted. If this
        // went through a shell the $(...) would execute and the ; would
        // split the command.
        $payload = 'Gerald; echo $(whoami)';

        $response = new Terminal()->runSafe(['echo', $payload]);

        $this->assertTrue($response->successful());
        $this->assertStringContainsString($payload, $response->output());
        // Proof no sub-shell ran: the command substitution is echoed verbatim.
        $this->assertStringContainsString('$(whoami)', $response->output());
    }

    public function testRunSafeReportsAFailingCommand(): void
    {
        $response = new Terminal()->dontThrow()->runSafe(['false']);

        $this->assertFalse($response->successful());
    }

    public function testRunShellIsAnExplicitAliasForRun(): void
    {
        $response = new Terminal()->runShell('echo Gerald');

        $this->assertTrue($response->successful());
        $this->assertStringContainsString('Gerald', $response->output());
    }

    public function testRunThrowsWhenACommandFailsByDefault(): void
    {
        $this->expectException(ProcessFailedException::class);

        new Terminal()->run('exit 3');
    }

    public function testWorkingDirectoryIsHonoured(): void
    {
        $response = new Terminal()->in('/')->runSafe(['pwd']);

        $this->assertTrue($response->successful());
        $this->assertStringContainsString('/', $response->output());
    }
}
