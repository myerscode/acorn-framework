<?php

namespace Tests\Resources\App\Commands;

use Myerscode\Acorn\Framework\Console\Command;

class SimpleOutputCommand extends Command
{

    protected string $signature = 'simple {name : Say hello}';

    protected string $description = 'A simple text command with text output.';

    public function handle(): void
    {
        $this->line("Hello " . $this->argument('name'));
    }
}
