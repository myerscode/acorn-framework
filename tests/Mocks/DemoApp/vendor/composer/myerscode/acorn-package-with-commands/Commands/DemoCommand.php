<?php

declare(strict_types=1);

namespace App\Commands;

use App\Events\DemoEvent;
use Myerscode\Acorn\Framework\Console\Command;

use function Myerscode\Acorn\Foundation\emit;

class DemoCommand extends Command
{

    #[\Override]
    protected string $signature = 'demo';

    #[\Override]
    protected string $description = 'A demo command.';

    public function handle(): void
    {
        emit(DemoEvent::class, time() + random_int(0, 1000));
    }
}
