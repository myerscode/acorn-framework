<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Application;
use Myerscode\Acorn\Framework\Console\Command;
use Override;

/**
 * Remove the discovery manifest written by acorn:optimize, returning the app
 * to live filesystem discovery.
 */
class ClearCacheCommand extends Command
{
    #[Override]
    protected string $description = 'Clear the cached discovery manifest';

    #[Override]
    protected string $signature = 'acorn:clear';

    public function handle(): int
    {
        $application = $this->getApplication();

        if (!$application instanceof Application) {
            $this->error('Clear requires the Acorn application.');

            return 1;
        }

        $manifest = $application->discoveryManifest();

        if ($manifest->delete()) {
            $this->info('Cleared the discovery manifest.');
        } else {
            $this->info('No discovery manifest to clear.');
        }

        return 0;
    }
}
