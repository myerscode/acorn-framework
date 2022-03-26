<?php

namespace Tests\Resources\App\Commands;

use Exception;
use Myerscode\Acorn\Framework\Console\Command;

class CommandThatErrorsCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('error-command');
    }

    function handle(): void
    {
        throw new Exception();
    }
}
