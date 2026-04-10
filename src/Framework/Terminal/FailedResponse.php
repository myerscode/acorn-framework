<?php

namespace Myerscode\Acorn\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;

class FailedResponse extends TerminalResponse
{
    public function setError(ProcessFailedException $processFailedException): self
    {
        $this->exception = $processFailedException;

        return $this;
    }
}
