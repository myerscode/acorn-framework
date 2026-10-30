<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\Config\Config;
use Myerscode\Utilities\Files\Exceptions\NotADirectoryException;
use Myerscode\Utilities\Files\Utility as FileService;

class Manager implements ManagerInterface
{
    protected bool $ignoreCached = false;

    protected bool $shouldCacheConfig = true;

    protected bool $usingCachedConfig = false;

    public function __construct(protected readonly string $rootPath)
    {
        //
    }

    /**
     * Tell the manager to never cache a compiled config file
     *
     * @return $this
     */
    public function doNotCacheConfig(): self
    {
        return $this->setShouldCacheConfig(false);
    }

    /**
     * Tell the manager to never ignore the cache
     *
     * @return $this
     */
    public function doNotIgnoreCache(): self
    {
        return $this->setIgnoreCachedConfig(false);
    }

    /**
     * Is the manager going to cache the config when loaded
     */
    public function isCachingConfig(): bool
    {
        return $this->shouldCacheConfig;
    }

    /**
     * Is the manager ignoring the cache
     */
    public function isIgnoringCache(): bool
    {
        return $this->ignoreCached;
    }

    /**
     * Check if the manager has loaded a configuration from a cache
     * If the manager has not loaded the configuration yet, it will always return false
     */
    public function isUsingCachedConfig(): bool
    {
        return $this->usingCachedConfig;
    }

    /**
     * @param string[] $configLocations
     * @param array<string, mixed> $data
     */
    public function loadConfig(array $configLocations, array $data = []): Config
    {
        $config = false;

        if (!$this->isIgnoringCache()) {
            $config = $this->cachedConfig();
        }

        if (!$config) {
            $config = $this->buildConfig($configLocations, $data);
            if ($this->isCachingConfig()) {
                $this->cacheConfig($config->values());
            }
        }

        return $config;
    }

    /**
     * Set if the manager should ignore the cache
     *
     * @return $this
     */
    public function setIgnoreCachedConfig(bool $ignoreCachedConfig): self
    {
        $this->ignoreCached = $ignoreCachedConfig;

        return $this;
    }

    /**
     * Set if the manager should cache the config
     *
     * @return $this
     */
    public function setShouldCacheConfig(bool $shouldCacheConfig): self
    {
        $this->shouldCacheConfig = $shouldCacheConfig;

        return $this;
    }

    /**
     * Tell the manager to cache a compiled config file
     *
     * @return $this
     */
    public function shouldCacheConfig(): self
    {
        return $this->setShouldCacheConfig(true);
    }

    /**
     * Tell the manager to ignore the cache, even if one exists
     *
     * @return $this
     */
    public function shouldIgnoreCache(): self
    {
        return $this->setIgnoreCachedConfig(true);
    }

    /**
     * @param string[] $configLocations
     * @param array<string, mixed> $data
     */
    protected function buildConfig(array $configLocations, array $data): Config
    {
        $config = new Config();

        $config->loadData([
            ...$data,
            'configLocations' => $configLocations,
        ]);

        foreach ($configLocations as $configLocation) {
            try {
                $configFiles = array_map(fn ($file) => $file->getRealPath(), FileService::make($configLocation)->files());
                $config->loadFilesWithNamespace($configFiles);
            } catch (NotADirectoryException) {
                //  TODO add debug output
            }
        }

        return $config;
    }

    /**
     * Cache the configuration as a JSON file (data-only, no code execution).
     *
     * @param array<string, mixed> $config
     */
    protected function cacheConfig(array $config): void
    {
        $cachedConfigLocation = $this->cacheLocation();

        $directory = dirname($cachedConfigLocation);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $json = json_encode($config, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($cachedConfigLocation, $json, LOCK_EX);
    }

    /**
     * Load the cached configuration from a JSON file.
     */
    protected function cachedConfig(): Config|false
    {
        $cachedConfigLocation = $this->cacheLocation();

        if (!file_exists($cachedConfigLocation)) {
            return false;
        }

        $contents = file_get_contents($cachedConfigLocation);
        if ($contents === false) {
            return false;
        }

        $data = json_decode($contents, true);
        if (!is_array($data)) {
            return false;
        }

        $config = new Config();
        $config->loadData($data);
        $config->loadData(['cachedConfig' => 'true', 'cachedConfigLocation' => $cachedConfigLocation]);

        $this->usingCachedConfig = true;

        return $config;
    }

    /**
     * Where is the cache being stored
     */
    protected function cacheLocation(): string
    {
        return $this->rootPath . '/.acorn/config.json';
    }
}
