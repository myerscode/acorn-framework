<?php

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;

use Myerscode\Utilities\Bags\DotUtility;

use Myerscode\Utilities\Bags\Utility;

use function Myerscode\Acorn\Foundation\config;

class ShowConfigCommand extends Command
{

    protected string $signature = 'config';

    protected string $description = 'Show all loaded configuration';

    public function handle(): void
    {
        $locations = config('configLocations');
        $bag = new Utility(config());
        $config = $bag->flatten()->toArray();

        $this->output->info("Loaded Config Files From Locations:");
        foreach ($locations as $location) {
            $this->output->text($location);
        }
//        dd($flattened);
        $mapped = array_map(fn($key, $value): array => ['key' => $key, 'value' => $value], array_keys($config), $config);
        $headers = ['Key', 'Value'];
        $this->output->newLine(2);
        $this->output->table($headers, $mapped);
    }
}
