<?php

declare(strict_types=1);

namespace Tests\Resources\App\Commands;

use Exception;
use Myerscode\Acorn\Framework\Console\Command;

class CommandThatErrorsCommand extends Command
{
    public function handle(): void
    {
        throw new Exception();
    }
    protected function configure(): void
    {
        $this->setName('error-command');
    }
}
