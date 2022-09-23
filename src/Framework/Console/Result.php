<?php

namespace Myerscode\Acorn\Framework\Console;

use Exception;

class Result
{
    public function __construct(protected $exitCode, protected ?Exception $exception = null)
    {
        //
    }

    public function wasSuccessful(): bool
    {
        return $this->exitCode === 0;
    }

    public function failed(): bool
    {
        return $this->exitCode !== 0;
    }

    public function exitCode(): int
    {
        return $this->exitCode;
    }

    public function error(): Exception|null
    {
        return $this->exception;
    }
}
