<?php

namespace Myerscode\Acorn\Framework\Terminal\Exception;

use Exception;
use Symfony\Component\Process\Exception\ProcessFailedException as SymfonyProcessFailedException;
use Symfony\Component\Process\Process;

class ProcessFailedException extends SymfonyProcessFailedException
{

    public function __construct(readonly Process $process, readonly ?Exception $previous = null)
    {
        $this->message = sprintf(
            'The command "%s" failed.' . "\n\nExit Code: %s(%s)\n\nWorking directory: %s",
            $this->process->getCommandLine(),
            $this->process->getExitCode(),
            $this->process->getExitCodeText(),
            $this->process->getWorkingDirectory()
        );

        if (!$this->process->isOutputDisabled()) {
            $this->message .= sprintf(
                "\n\nOutput:\n================\n%s\n\nError Output:\n================\n%s",
                $this->process->getOutput(),
                $this->process->getErrorOutput()
            );
        }
    }
}
