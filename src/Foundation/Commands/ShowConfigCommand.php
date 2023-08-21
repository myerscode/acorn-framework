<?php

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Utilities\Bags\Utility;

use function Myerscode\Acorn\Foundation\config;

class ShowConfigCommand extends Command
{
    protected string $signature = 'config:show';

    protected string $description = 'Show all loaded configuration';

    public function handle(): void
    {
        if (config('cachedConfig')) {
            $locations = [config('cachedConfigLocation')];
        } else {
            $locations = config('configLocations');
        }

        $bag = new Utility(config());
        $config = $bag->flatten()->toArray();

        $mapped = array_map(fn($key, $value): array => [$value], array_keys($locations), $locations);

        $this->output->table(["Config Loaded From Locations"], $mapped)->render();

        $mapped = array_map(fn($key, $value): array => ['key' => $key, 'value' => $value], array_keys($config), $config);
        $headers = ['Key', 'Value'];
        $this->output->table($headers, $mapped)->render();
    }
}
