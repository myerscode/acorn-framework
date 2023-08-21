<?php

namespace Myerscode\Acorn\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;

abstract class TerminalResponse implements TerminalResponseInterface
{
    protected ?ProcessFailedException $exception;

    public function __construct(protected Process $process, protected int $attempt, protected int $sleptFor = 0)
    {
    }
//
//    /**
//     * Get the process output.
//     *
//     * @return string
//     */
//    public function __toString()
//    {
//        return $this->output();
//    }
//

    /**
     * @inheritDoc
     */
    public function attempt(): int
    {
        return $this->attempt;
    }

    /**
     * @inheritDoc
     */
    public function error(): ?ProcessFailedException
    {
        return (isset($this->exception)) ? $this->exception : null;
    }

    /**
     * @inheritDoc
     */
    public function exitCode(): int|null
    {
        return $this->process()->getExitCode();
    }

    /**
     * @inheritDoc
     */
    public function failed(): bool
    {
        return !$this->successful();
    }

    /**
     * @inheritDoc
     */
    public function output(): string
    {
        return trim($this->process()->getOutput());
    }

    /**
     * @inheritDoc
     */
    public function sleptFor(): int
    {
        return $this->sleptFor;
    }

    /**
     * @inheritDoc
     */
    public function successful(): bool
    {
        return $this->process()->isSuccessful();
    }

    /**
     * @inheritDoc
     */
    public function throw(): void
    {
        if (!$this->successful()) {
            throw new ProcessFailedException($this->process());
        }
    }

    /**
     * Get the underlying process instance.
     */
    public function process(): Process
    {
        return $this->process;
    }
}
