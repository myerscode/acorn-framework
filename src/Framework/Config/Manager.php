<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\Config\Config;
use Myerscode\Utilities\Files\Exceptions\NotADirectoryException;
use Myerscode\Utilities\Files\Utility as FileService;

class Manager
{
    protected bool $shouldCacheConfig = true;

    protected bool $ignoreCached = false;

    public function __construct(protected readonly string $basePath)
    {
        //
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

        $appRoot = dirname($this->basePath);

        $cachedConfigLocation = $appRoot . '/.acorn/config.php';

        $cachedConfig = FileService::make($cachedConfigLocation);

        if (!$cachedConfig->exists()) {
            return false;
        }

        $config->loadFile($cachedConfig->path());

        $config->loadData(['cachedConfig' => 'true', 'cachedConfigLocation' => $cachedConfigLocation]);

        return $config;
    }

    protected function cacheConfig(array $config): void
    {
        $appRoot = dirname($this->basePath);

        $cachedConfigLocation = $appRoot . '/.acorn/config.php';

        $template = "<?php return " . var_export($config, true) . ";";

        FileService::make($cachedConfigLocation)->touch()->setContent($template);
    }

    public function loadConfig(array $configLocations, array $data): Config
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
     * Should the manager ignoring the cache
     * @return bool
     */
    public function isIgnoringCache(): bool
    {
        return $this->ignoreCached;
    }

    public function dontIgnoreCache(): self
    {
        return $this->setIgnoreCachedConfig(false);
    }

    public function shouldIgnoreCache(): self
    {
        return $this->setIgnoreCachedConfig(true);
    }

    public function setIgnoreCachedConfig(bool $ignoreCachedConfig): self
    {
        $this->ignoreCached = $ignoreCachedConfig;

        return $this;
    }

    /**
     * Is the manager going to cache the config when loaded
     * @return bool
     */
    public function isCachingConfig(): bool
    {
        return $this->shouldCacheConfig;
    }

    public function doNotCacheConfig(): self
    {
        return $this->setShouldCacheConfig(false);
    }

    public function shouldCacheConfig(): self
    {
        return $this->setShouldCacheConfig(true);
    }

    public function setShouldCacheConfig(bool $shouldCacheConfig): self
    {
        $this->shouldCacheConfig = $shouldCacheConfig;

        return $this;
    }
}
