<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Exceptions;

use RuntimeException;

/**
 * Thrown by Command::fail() to terminate a command immediately with a chosen
 * exit code. The exit code is carried on the exception so the kernel can use
 * it as the process exit status.
 */
class CommandFailedException extends RuntimeException
{
    public function __construct(string $message = '', private readonly int $exitCode = 1)
    {
        parent::__construct($message, $exitCode);
    }

    public function exitCode(): int
    {
        return $this->exitCode;
    }
}
