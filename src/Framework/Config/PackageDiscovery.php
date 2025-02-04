<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\PackageDiscovery\Finder;

class PackageDiscovery
{
    readonly public array $found;
    readonly protected array $providers;
    readonly protected array $commands;
    private Finder $finder;

    public function __construct(string $root)
    {
        $this->finder = new Finder($root);

        $this->found = $this->finder->discover('acorn');
    }

    /**
     * Discover all command directories registered by installed packages
     *
     * @return array
     */
    public function locateCommands(): array
    {
        $commandLocations = [];

        foreach ($this->found as $package => $meta) {
            $commands = $meta['commands'] ?? false;
            if ($commands === true) {
                $packageLocation = $this->locatePackage($package);
                $commandDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands';
                $commandLocations[$package] = $commandDirectory;
            }
        }

        return $commandLocations;
    }

    /**
     * Discover all provider directories registered by installed packages
     *
     * @return array
     */
    public function locateProviders(): array
    {
        $providerClasses = [];

        foreach ($this->found as $package => $meta) {
            $providers = $meta['providers'] ?? false;
            if ($providers === true) {
                $packageLocation = $this->locatePackage($package);
                $providerDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Providers';
                $providerClasses[$package] = $providerDirectory;
            }
        }

        return $providerClasses;
    }

    protected function locatePackage(string $package): string
    {
        return trim($this->finder->locate($package));
    }
}
