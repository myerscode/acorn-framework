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
     * Environment variables.
     *
     * @var array $environmentVariables
     */
    protected array $environmentVariables = [];

    /**
     * Timeout.
     */
    protected int $timeout = 60;

    /**
     * Number times to try the command
     */
    protected int $retries = 0;

    /**
     * Seconds to sleep between retries
     */
    protected int $sleep = 0;

    /**
     * Current working directory.
     */
    protected string $cwd;

    /**
     * TTY mode.
     */
    protected bool $tty = false;

    /**
     * Should throw an exception if the command fails
     */
    protected bool $shouldThrow = true;

    /**
     * Number of attempts the terminal has made to run a process
     *
     * @var int
     */
    protected int $attempts = 0;

    /**
     * What to do with the output of the process
     */
    protected Closure|DisplayOutput $output;

    /**
     * The command that will be executed
     */
    protected Command|string $command = '';

    /**
     * Determine if a process should execute in the background.
     *
     * @var boolean
     */
    protected bool $inBackground = false;

    /**
     * Max time since last output.
     *
     * @var mixed
     */
    protected ?int $idleTimeout = null;

    /**
     * Execute a process in the background.
     *
     * @return $this
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
     *
     * @return int
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
     * @param  string|Command  $command
     *
     * @return self
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
     * @param  string  $cwd
     *
     * @return Terminal
     */
    public function in(string $cwd): self
    {
        $this->cwd = $cwd;

        return $this;
    }

    /**
     * Will the process be async or blocking
     *
     * @return bool
     */
    public function inBackground(): bool
    {
        return $this->inBackground;
    }

    /**
     * Is TTY supported on the running platform
     *
     * @return bool
     */
    public function isTtySupported(): bool
    {
        return Process::isTtySupported();
    }

    /**
     * Set output handler.
     *
     * @param  Closure|DisplayOutput  $output
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
     *
     * @return Process
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
        } else {
            if ($this->tty && !$this->isTtySupported()) {
                output()->warning("Tried setting tty on Terminal command - but it is not supported!");
            }
        }

        if (!is_null($this->idleTimeout())) {
            $process->setIdleTimeout($this->idleTimeout());
        }

        return $process;
    }

    /**
     * Retry an operation a given number of times.
     *
     * @param  int  $times
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
     * @param  string|Command  $command
     * @param  callable|DisplayOutputInterface|null  $output
     *
     * @return TerminalResponse
     * @throws ProcessFailedException
     */
    public function run(string|Command $command, callable|DisplayOutputInterface $output = null): TerminalResponse
    {
        $this->command($command);

        if (!is_null($output)) {
            $this->output($output);
        }

        $this->attempts = 0;

        return $this->executeAttempts($this->retries, function (int $attempt, int $sleptFor) {
            $this->attempts = $attempt;

            $process = $this->process();

            $response = $this->executeProcess($process, $attempt, $sleptFor);

            if ($this->inBackground() || $response->successful()) {
                return $response;
            }

            throw new ProcessFailedException($response->process());
        }, $this->sleepsFor());
    }

    public function shouldThrow(): bool
    {
        return $this->shouldThrow;
    }

    /**
     * Retry an operation a given number of times.
     *
     * @param  int  $sleep
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
     *
     * @return int
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
     * @param  int  $ttl
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
     * @param  int|null  $idleTimeout
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
     * @return $this
     */
    public function withEnvironmentVariables(array $envVars): self
    {
        $this->environmentVariables = $envVars;

        return $this;
    }

    /**
     * @return mixed
     * @throws ProcessFailedException
     */
    protected function executeAttempts(int $times, callable $callback, int $sleep = 0, int $sleptFor = 0): TerminalResponse
    {
        $attempts = 0;

        beginning:
        $attempts++;
        $times--;

        try {
            return $callback($attempts, $sleptFor);
        } catch (ProcessFailedException  $e) {
            if ($times < 1) {
                if ($this->shouldThrow()) {
                    throw $e;
                }

                return new FailedResponse($e->process, $attempts, $sleptFor);
            }

            if ($sleep) {
                $sleptFor += $sleep;
                usleep($sleep * 1000);
            }

            goto beginning;
        }
    }

    protected function executeProcess(Process $process, int $attempt, int $sleptFor): TerminalResponse
    {
        try {
            $callback = $this->output ?? null;

            if ($this->inBackground()) {
                $process->start($callback);
            } else {
                $process->run($callback);
            }

            return new CompletedResponse($process, $attempt, $sleptFor);
        } catch (Exception $exception) {
            throw new ProcessFailedException($process, $exception);
        }
    }

    /**
     * Wrap any given output callback for handling command outputs
     *
     * @param  callable|DisplayOutputInterface  $output
     *
     * @return Closure
     */
    protected function parseCallback(callable|DisplayOutputInterface $output): Closure
    {
        if ($output instanceof DisplayOutputInterface) {
            return function ($type, $data) use ($output) {
                return $output->output->write(trim($data));
            };
        }

        return function ($type, $data) use ($output) {
            return $output(trim($data));
        };
    }

    /**
     * Enable or disable the TTY mode.
     *
     * @param  bool  $tty
     *
     * @return $this
     */
    protected function tty(bool $tty): self
    {
        $this->tty = $tty;

        return $this;
    }
}
