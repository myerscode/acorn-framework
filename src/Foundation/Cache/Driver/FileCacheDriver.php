<?php

namespace Myerscode\Acorn\Foundation\Cache\Driver;

use Myerscode\Acorn\Framework\Cache\DriverInterface;

class FileCacheDriver implements DriverInterface
{
    public function __construct(protected readonly string $cacheDir)
    {
        //
    }

    public function cacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $cacheFiles = glob($this->cacheDir . '/*');
        foreach ($cacheFiles as $cacheFile) {
            unlink($cacheFile);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        $cacheFiles = glob($this->cacheDir . '/*');

        return count($cacheFiles);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile)) {
            unlink($cacheFile);

            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function deleteMultiple(iterable $keys): bool
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null): mixed
    {
        $cacheFile = $this->getCacheFile($key);
        if (!file_exists($cacheFile)) {
            return $default;
        }

        $data = json_decode(file_get_contents($cacheFile), true);

        if (time() > $data['expiration']) {
            // Cache is expired, delete it and return the default value
            unlink($cacheFile);

            return $default;
        }

        return $data['value'];
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = $this->get($key, $default);
        }

        return $values;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        $cacheFile = $this->getCacheFile($key);

        return file_exists($cacheFile);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, $value, $ttl = null): bool
    {
        $cacheFile = $this->getCacheFile($key);

        $data = [
            'value' => $value,
            'expiration' => $ttl !== null ? time() + $ttl : PHP_INT_MAX,
        ];

        file_put_contents($cacheFile, json_encode($data));

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * Get the path for the file where the cache data is stored
     */
    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
