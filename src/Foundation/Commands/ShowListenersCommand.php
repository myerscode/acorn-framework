<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;

use function Myerscode\Acorn\Foundation\config;

class ShowListenersCommand extends Command
{
    #[\Override]
    protected string $signature = 'listener:show';

    #[\Override]
    protected string $description = 'Show all registered event listeners';

    public function handle(): void
    {
        $this->output->writeln("App Event Listeners");

        $listenerLocations = [
            'app' => config('app.listeners'),
            'framework' => config('framework.listeners'),
            'executing' => config('executing.listeners'),
        ];

        print_r($listenerLocations);
    }
}
