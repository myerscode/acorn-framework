<?php

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;

use function Myerscode\Acorn\Foundation\config;

class ShowListenersCommand extends Command
{
    protected string $signature = 'listener:show';

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
