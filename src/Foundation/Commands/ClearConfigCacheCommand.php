<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Override;

use function Myerscode\Acorn\Foundation\config;
use function Myerscode\Acorn\Foundation\terminal;

class ClearConfigCacheCommand extends Command
{
    #[Override]
    protected string $description = 'Clear any config cache';

    #[Override]
    protected string $signature = 'config:clear';

    public function handle(): void
    {
        $cachedLocation = config('cachedConfigLocation');

        $this->info('Clearing ' . $cachedLocation);

        terminal()->run('rm -f ' . $cachedLocation);
    }
}
