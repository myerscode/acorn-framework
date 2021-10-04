<?php

namespace Myerscode\Acorn\Framework\Console;

use Exception;

class Result
{
    public function __construct(protected $exitCode, protected ?Exception $error = null)
    {
        //
    }

    public function wasSucessfull(): int
    {
        return $this->exitCode === 0;
    }

    public function failed(): int
    {
        return $this->exitCode !== 0;
    }

    public function exitCode(): int
    {
        return $this->exitCode;
    }

    public function error(): Exception
    {
        return $this->error;
    }
}
