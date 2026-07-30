<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Utilities\Bags\Utility;
use Override;

use function Myerscode\Acorn\Foundation\config;

class ShowConfigCommand extends Command
{
    #[Override]
    protected string $description = 'Show all loaded configuration';

    #[Override]
    protected string $signature = 'config:show';

    public function handle(): int
    {
        $this->output->warning('This command displays all configuration values including filesystem paths. Do not use in untrusted environments.');

        if (config('cachedConfig')) {
            $locations = [config('cachedConfigLocation')];
        } else {
            $locations = config('configLocations');
        }

        $utility = new Utility(config());
        $config = $utility->flatten()->toArray();

        $mapped = array_map(fn ($key, $value): array => [$value], array_keys((array) $locations), (array) $locations);

        $this->output->table(['Config Loaded From Locations'], $mapped);

        $mapped = array_map(fn ($key, $value): array => ['key' => $key, 'value' => $value], array_keys($config), $config);
        $headers = ['Key', 'Value'];
        $this->output->table($headers, $mapped);

        return 0;
    }
}
