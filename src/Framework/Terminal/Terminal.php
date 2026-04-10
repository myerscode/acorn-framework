<?php

namespace Myerscode\Acorn\Framework\Terminal;

use Closure;
use Exception;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;

use function Myerscode\Acorn\Foundation\output;

class Terminal
{
    /**
     * Number of attempts the terminal has made to run a process
     */
    protected int $attempts = 0;

    /**
     * The command that will be executed
     */
    protected Command|string $command = '';

    /**
     * Current working directory.
     */
    protected string $cwd;

    /**
     * Environment variables.
     *
     * @var array<string, string>
     */
    protected array $environmentVariables = [];

    /**
     * Max time since last output.
     */
    protected ?int $idleTimeout = null;

    /**
     * Determine if a process should execute in the background.
     */
    protected bool $inBackground = false;

    /**
     * What to do with the output of the process
     */
    protected Closure|DisplayOutput $output;

    /**
     * Number times to try the command
     */
    protected int $retries = 0;

    /**
     * Should throw an exception if the command fails
     */
    protected bool $shouldThrow = true;

    /**
     * Seconds to sleep between retries
     */
    protected int $sleep = 0;

    /**
     * Timeout.
     */
    protected int $timeout = 60;

    /**
     * TTY mode.
     */
    protected bool $tty = false;

    /**
     * Execute a process in the background.
     *
     * @throws ProcessFailedException
     */
    public function async(string|Command $command): TerminalResponse
    {
        $this->inBackground = true;

        $this->retries = 0;

        return $this->run($command);
    }

    /**
     * Number of attempts the terminal has made to run a process
     *
     * Will be 0 before the process is run and is reset every time run() is called
     */
    public function attempts(): int
    {
        return $this->attempts;
    }

    /**
     * Execute a process in the background.
     *
     * @return $this
     */
    public function block(): self
    {
        $this->inBackground = false;

        return $this;
    }

    /**
     * Set the command to be run
     *
     *
     */
    public function command(string|Command $command): self
    {
        $this->command = new Command($command);

        return $this;
    }

    /**
     * Disable TTY mode.
     *
     * @return $this
     */
    public function disableTty(): self
    {
        return $this->tty(false);
    }

    public function dontThrow(): self
    {
        $this->shouldThrow = false;

        return $this;
    }

    /**
     * Disable timing out the command if there has been no output
     *
     * @return $this
     */
    public function dontTimeoutWhenOutputIsIdle(): self
    {
        return $this->timeoutWhenOutputIsIdle(null);
    }

    /**
     * Enable TTY mode.
     *
     * @return $this
     */
    public function enableTty(): self
    {
        return $this->tty(true);
    }

    /**
     * @return array<string, string>
     */
    public function environmentVariables(): array
    {
        return $this->environmentVariables;
    }

    /**
     * How long between outputs will the terminal wait
     *
     * @return int
     */
    public function idleTimeout(): ?int
    {
        return $this->idleTimeout;
    }

    /**
     * Set current working directory.
     *
     *
     */
    public function in(string $cwd): self
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Will the process be async or blocking
     */
    public function inBackground(): bool
    {
        return $this->inBackground;
    }

    /**
     * Is TTY supported on the running platform
     */
    public function isTtySupported(): bool
    {
        return Process::isTtySupported();
    }

    /**
     * Set output handler.
     *
     *
     * @return $this
     */
    public function output(Closure|DisplayOutput $output): self
    {
        $this->output = $this->parseCallback($output);

        return $this;
    }

    /**
     * Create the underlying Symfony Process to run
     */
    public function process(): Process
    {
        $parameters = [
            (is_string($this->command) ? Command::make($this->command) : $this->command)->instructions(),
            $this->cwd ?? null,
            $this->environmentVariables(),
            '',
            $this->timeout,
        ];

        $process = Process::fromShellCommandline(...$parameters);

        if ($this->tty && $this->isTtySupported()) {
            $process->setTty($this->tty);
        } elseif ($this->tty && !$this->isTtySupported()) {
            output()->warning('Tried setting tty on Terminal command - but it is not supported!');
        }

        if (!is_null($this->idleTimeout())) {
            $process->setIdleTimeout($this->idleTimeout());
        }

        return $process;
    }

    /**
     * Retry an operation a given number of times.
     *
     *
     * @return $this
     */
    public function retries(int $times): self
    {
        $this->retries = $times;

        return $this;
    }

    /**
     * Execute a given command.
     *
     * @throws ProcessFailedException
     */
    public function run(string|Command $command, callable|DisplayOutputInterface|null $output = null): TerminalResponse
    {
        $this->command($command);

        if (!is_null($output)) {
            $this->output = $this->parseCallback($output);
        }

        $this->attempts = 0;

        return $this->executeAttempts($this->retries, function (int $attempt, int $sleptFor): TerminalResponse {
            $this->attempts = $attempt;

            $process = $this->process();

            $terminalResponse = $this->executeProcess($process, $attempt, $sleptFor);

            if ($this->inBackground() || $terminalResponse->successful()) {
                return $terminalResponse;
            }

            throw new ProcessFailedException($terminalResponse->process());
        }, $this->sleepsFor());
    }

    public function shouldThrow(): bool
    {
        return $this->shouldThrow;
    }

    /**
     * Retry an operation a given number of times.
     *
     *
     * @return $this
     */
    public function sleep(int $sleep = 0): self
    {
        $this->sleep = $sleep;

        return $this;
    }

    /**
     * How long the terminal sleeps for between a failed operation (in seconds)
     */
    public function sleepsFor(): int
    {
        return $this->sleep;
    }

    public function throw(): self
    {
        $this->shouldThrow = true;

        return $this;
    }

    /**
     * Set Process timeout in seconds.
     *
     *
     * @return $this
     */
    public function timeout(int $ttl): self
    {
        $this->timeout = $ttl;

        return $this;
    }

    /**
     * Set max time since last output.
     *
     *
     * @return $this
     */
    public function timeoutWhenOutputIsIdle(?int $idleTimeout): self
    {
        $this->idleTimeout = $idleTimeout;

        return $this;
    }

    /**
     * Set environment variables that the process will use.
     *
     * @param array<string, string> $envVars
     * @return $this
     */
    public function withEnvironmentVariables(array $envVars): self
    {
        $this->environmentVariables = $envVars;

        return $this;
    }

    /**
     * @throws ProcessFailedException
     */
    protected function executeAttempts(int $times, callable $callback, int $sleep = 0, int $sleptFor = 0): TerminalResponse
    {
        $attempts = 0;
        $lastException = null;
        $maxAttempts = $times + 1;

        while ($attempts < $maxAttempts) {
            $attempts++;

            try {
                return $callback($attempts, $sleptFor);
            } catch (ProcessFailedException $processFailedException) {
                $lastException = $processFailedException;

                if ($attempts >= $maxAttempts) {
                    if ($this->shouldThrow()) {
                        throw $processFailedException;
                    }

                    return new FailedResponse($processFailedException->process, $attempts, $sleptFor);
                }

                if ($sleep !== 0) {
                    $sleptFor += $sleep;
                    usleep($sleep * 1000);
                }
            }
        }

        if ($lastException !== null) {
            return new FailedResponse($lastException->process, $attempts, $sleptFor);
        }

        return new FailedResponse(new Process([]), $attempts, $sleptFor);
    }

    protected function executeProcess(Process $process, int $attempt, int $sleptFor): TerminalResponse
    {
        try {
            $callback = isset($this->output) ? $this->output : null;

            if ($this->inBackground()) {
                $process->start(is_callable($callback) ? $callback : null);
            } else {
                $process->run(is_callable($callback) ? $callback : null);
            }

            return new CompletedResponse($process, $attempt, $sleptFor);
        } catch (Exception $exception) {
            throw new ProcessFailedException($process, $exception);
        }
    }

    /**
     * Wrap any given output callback for handling command outputs
     *
     *
     */
    protected function parseCallback(callable|DisplayOutputInterface $output): Closure
    {
        if ($output instanceof DisplayOutputInterface) {
            return function ($type, $data) use ($output): void {
                $output->write(trim($data));
            };
        }

        return function ($type, $data) use ($output): void {
            $output(trim($data));
        };
    }

    /**
     * Enable or disable the TTY mode.
     *
     *
     * @return $this
     */
    protected function tty(bool $tty): self
    {
        $this->tty = $tty;

        return $this;
    }
}
