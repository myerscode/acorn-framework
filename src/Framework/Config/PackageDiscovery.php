<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\PackageDiscovery\Finder;

class PackageDiscovery
{
    /** @var array<string, array<string, mixed>> */
    public readonly array $found;

    private readonly Finder $finder;

    public function __construct(string $root)
    {
        $this->finder = new Finder($root);

        $this->found = $this->finder->discover('acorn');
    }

    /**
     * Discover all command directories registered by installed packages
     *
     * @return array<string, string>
     */
    public function locateCommands(): array
    {
        $commandLocations = [];

        foreach ($this->found as $package => $meta) {
            $commands = $meta['commands'] ?? false;
            if ($commands === true) {
                $packageLocation = $this->locatePackage($package);
                $commandDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Commands';

                if (!$this->isWithinPackage($commandDirectory, $packageLocation)) {
                    continue;
                }

                $commandLocations[$package] = $commandDirectory;
            }
        }

        return $commandLocations;
    }

    /**
     * Discover all provider directories registered by installed packages
     *
     * @return array<string, string>
     */
    public function locateProviders(): array
    {
        $providerClasses = [];

        foreach ($this->found as $package => $meta) {
            $providers = $meta['providers'] ?? false;
            if ($providers === true) {
                $packageLocation = $this->locatePackage($package);
                $providerDirectory = $packageLocation . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Providers';

                if (!$this->isWithinPackage($providerDirectory, $packageLocation)) {
                    continue;
                }

                $providerClasses[$package] = $providerDirectory;
            }
        }

        return $providerClasses;
    }

    protected function locatePackage(string $package): string
    {
        return trim($this->finder->locate($package));
    }

    /**
     * Validate that a discovered path is within the package's own directory.
     * Prevents path traversal attacks via malicious composer.json metadata.
     */
    private function isWithinPackage(string $path, string $packageRoot): bool
    {
        $resolvedPath = realpath($path) ?: $path;
        $resolvedRoot = realpath($packageRoot) ?: $packageRoot;

        // Normalise trailing separators for comparison
        $resolvedRoot = rtrim($resolvedRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return str_starts_with($resolvedPath, $resolvedRoot);
    }
}
