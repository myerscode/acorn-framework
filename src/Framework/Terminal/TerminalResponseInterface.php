<?php

namespace Myerscode\Acorn\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;

interface TerminalResponseInterface
{
    /**
     * How many attempts did this process take
     */
    public function attempt(): int;

    /**
     * Get the error that thrown in the process
     */
    public function error(): ?ProcessFailedException;

    /**
     * Exit code of the process
     */
    public function exitCode(): int|null;

    /**
     * Did the process fail
     */
    public function failed(): bool;

    /**
     * Get the standard output of the process
     */
    public function output(): string;

    /**
     * How long the command slept for in total
     */
    public function sleptFor(): int;

    /**
     * Was the process successful
     */
    public function successful(): bool;

    /**
     * Throw an exception if the process was not successful.
     *
     * @throws ProcessFailedException
     */
    public function throw(): void;
}
