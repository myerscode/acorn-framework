<?php

namespace Myerscode\Acorn\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;

class FailedResponse extends TerminalResponse
{
    public function setError(ProcessFailedException $exception): self
    {
        $this->exception = $exception;

        return $this;
    }
}
