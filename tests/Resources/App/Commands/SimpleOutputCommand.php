<?php

declare(strict_types=1);

namespace Tests\Resources\App\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Override;

class SimpleOutputCommand extends Command
{
    #[Override]
    protected string $description = 'A simple text command with text output.';

    #[Override]
    protected string $signature = 'simple {name : Say hello}';

    public function handle(): int
    {
        $this->line('Hello ' . $this->argument('name'));
        return 0;
    }
}
