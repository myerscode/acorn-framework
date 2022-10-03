<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\PackageDiscovery\Finder;

class PackageDiscovery
{
    private Finder $finder;

    readonly public array $found;

    readonly public array $providers;

    readonly public array $commands;

    public function __construct(string $root)
    {
        $this->finder = new Finder($root);

        $this->found = $this->finder->discover('acorn');

        $this->providers = $this->locateProviders();

        $this->commands = $this->locateCommands();
    }

    public function locateCommands(): array
    {
        $commandLocations = [];

        foreach ($this->found as $package => $meta) {
            if (($commands = $meta['commands']) && $commands === true) {
                $packageLocation = $this->finder->locate($package);
//                dd($packageLocation);
                $commandDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'app/Commands';
                $commandLocations[$package] = $commandDirectory;
            }
        }

        return $commandLocations;
    }

    public function locateProviders(): array
    {
        $providers = [ ];

        foreach ($this->found as $package => $meta) {
//            if ($commands = $meta['commands']) {
//                if ($commands === true) {
//                    $packageLocation = $this->finder->locate($package);
//                    $commandDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'Commands';
//                    $providers['commands'][$package] = $commandDirectory;
//                }
//            }
        }

        return $providers;
    }
}
