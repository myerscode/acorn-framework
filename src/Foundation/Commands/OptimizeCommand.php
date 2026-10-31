<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Application;
use Myerscode\Acorn\Framework\Console\Command;
use Override;

/**
 * Pre-scan command, provider and listener discovery and cache the result to a
 * manifest, so subsequent boots skip the filesystem scan and reflection.
 */
class OptimizeCommand extends Command
{
    #[Override]
    protected string $description = 'Cache discovered commands, providers and listeners for faster boots';

    #[Override]
    protected string $signature = 'acorn:optimize';

    public function handle(): int
    {
        $application = $this->getApplication();

        if (!$application instanceof Application) {
            $this->error('Optimize requires the Acorn application.');

            return 1;
        }

        $counts = $application->optimize();

        $this->info(sprintf(
            'Cached %d command(s), %d provider(s) and %d listener(s) to %s',
            $counts['commands'],
            $counts['providers'],
            $counts['listeners'],
            $application->discoveryManifest()->path(),
        ));

        return 0;
    }
}
