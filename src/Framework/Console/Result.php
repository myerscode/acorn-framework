<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console;

use Exception;

class Result
{
    public function __construct(protected int $exitCode, protected ?Exception $exception = null)
    {
        //
    }

    public function error(): Exception|null
    {
        return $this->exception;
    }

    public function exitCode(): int
    {
        return $this->exitCode;
    }

    public function failed(): bool
    {
        return $this->exitCode !== 0;
    }

    public function wasSuccessful(): bool
    {
        return $this->exitCode === 0;
    }
}
