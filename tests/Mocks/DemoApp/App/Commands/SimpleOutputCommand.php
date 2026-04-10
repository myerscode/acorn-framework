<?php

declare(strict_types=1);

namespace Tests\Mocks\DemoApp\App\Commands;

use Myerscode\Acorn\Framework\Console\Command;

class SimpleOutputCommand extends Command
{

    #[\Override]
    protected string $signature = 'simple {name : Say hello}';

    #[\Override]
    protected string $description = 'A simple text command with text output.';

    public function handle(): void
    {
        $this->line("Hello " . $this->argument('name'));
    }
}
