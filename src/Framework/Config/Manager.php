<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\Config\Config;
use Myerscode\Utilities\Files\Exceptions\NotADirectoryException;
use Myerscode\Utilities\Files\Utility as FileService;

class Manager implements ManagerInterface
{
    protected bool $shouldCacheConfig = true;

    protected bool $ignoreCached = false;

    protected bool $usingCachedConfig = false;

    public function __construct(protected readonly string $rootPath)
    {
        //
    }

    /**
     * Where is the cache being stored
     *
     * @return string
     */
    protected function cacheLocation(): string
    {
        return $this->rootPath . '/.acorn/config.php';
    }

    protected function buildConfig(array $configLocations, array $data): Config
    {
        $config = new Config();

        $config->loadData([
            ...$data,
            'configLocations' => $configLocations,
        ]);

        foreach ($configLocations as $configLocation) {
            try {
                $configFiles = array_map(fn($file) => $file->getRealPath(), FileService::make($configLocation)->files());
                $config->loadFilesWithNamespace($configFiles);
            } catch (NotADirectoryException) {
                //  TODO add debug output
            }
        }

        return $config;
    }

    protected function cachedConfig(): Config|false
    {
        $config = new Config();

        $cachedConfigLocation = $this->cacheLocation();

        $cachedConfig = FileService::make($cachedConfigLocation);

        if (!$cachedConfig->exists()) {
            return false;
        }

        $config->loadFile($cachedConfig->path());

        $config->loadData(['cachedConfig' => 'true', 'cachedConfigLocation' => $cachedConfigLocation]);

        $this->usingCachedConfig = true;

        return $config;
    }

    protected function cacheConfig(array $config): void
    {
        $cachedConfigLocation = $this->cacheLocation();

        $template = "<?php return " . var_export($config, true) . ";";

        FileService::make($cachedConfigLocation)->touch()->setContent($template);
    }

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
     * Is the manager ignoring the cache
     *
     * @return bool
     */
    public function isIgnoringCache(): bool
    {
        return $this->ignoreCached;
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
     * Tell the manager to ignore the cache, even if one exists
     *
     * @return $this
     */
    public function shouldIgnoreCache(): self
    {
        return $this->setIgnoreCachedConfig(true);
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
     * Check if the manager has loaded a configuration from a cache
     * If the manager has not loaded the configuration yet, it will always return false
     *
     * @return bool
     */
    public function isUsingCachedConfig(): bool
    {
        return $this->usingCachedConfig;
    }

    /**
     * Is the manager going to cache the config when loaded
     *
     * @return bool
     */
    public function isCachingConfig(): bool
    {
        return $this->shouldCacheConfig;
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
     * Tell the manager to cache a compiled config file
     *
     * @return $this
     */
    public function shouldCacheConfig(): self
    {
        return $this->setShouldCacheConfig(true);
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
}
