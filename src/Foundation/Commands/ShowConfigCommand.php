<?php

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Config\Config;
use function Myerscode\Acorn\Foundation\config;
use function Myerscode\Acorn\Foundation\container;

class ShowConfigCommand extends Command
{

    protected string $signature = 'config';

    protected string $description = 'Show all loaded configuration';

    public function handle(): void
    {
        $locations = config('configLocations');
        $config = container('config');
        $flattened = $config->store()->flatten();
        $mapped = array_map(fn($key, $value): array => ['key' => $key, 'value' => $value], array_keys($flattened), $flattened);
        $headers = ['Key', 'Value'];
        $this->output->info("Loaded Config Files From Locations:");
        foreach ($locations as $location) {
            $this->output->text($location);
        }

        $this->output->newLine(2);
        $this->output->table($headers, $mapped);
    }
}
