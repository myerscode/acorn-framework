<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Override;

use function Myerscode\Acorn\Foundation\config;

class ShowListenersCommand extends Command
{
    #[Override]
    protected string $description = 'Show all registered event listeners';

    #[Override]
    protected string $signature = 'listener:show';

    public function handle(): int
    {
        $this->info('Event Listener Directories');

        $listenerLocations = [
            'app' => config('app.dir.listeners'),
            'framework' => config('framework.dir.listeners'),
            'executing' => config('executing.dir.listeners'),
        ];

        $rows = array_map(
            fn ($source, $path): array => [$source, $path ?? 'not configured'],
            array_keys($listenerLocations),
            $listenerLocations,
        );

        $this->output->table(['Source', 'Directory'], $rows);

        return 0;
    }
}
