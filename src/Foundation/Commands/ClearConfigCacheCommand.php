<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Override;

use function Myerscode\Acorn\Foundation\config;

class ClearConfigCacheCommand extends Command
{
    #[Override]
    protected string $description = 'Clear any config cache';

    #[Override]
    protected string $signature = 'config:clear';

    public function handle(): void
    {
        $cachedLocation = config('cachedConfigLocation');

        if (empty($cachedLocation) || !is_string($cachedLocation)) {
            $this->info('No config cache location configured.');
            return;
        }

        $this->info('Clearing ' . $cachedLocation);

        if (file_exists($cachedLocation)) {
            unlink($cachedLocation);
        }
    }
}
