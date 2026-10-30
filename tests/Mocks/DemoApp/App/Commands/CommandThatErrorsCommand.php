<?php

declare(strict_types=1);

namespace Tests\Mocks\DemoApp\App\Commands;

use Exception;
use Myerscode\Acorn\Framework\Console\Command;

class CommandThatErrorsCommand extends Command
{
    public function handle(): int
    {
        throw new Exception();
    }

    protected function configure(): void
    {
        $this->setName('error-command');
    }
}
