<?php

namespace Myerscode\Acorn\Foundation\Cache\Driver;

use DateInterval;
use Myerscode\Acorn\Framework\Cache\DriverInterface;

class RuntimeCache implements DriverInterface
{
    protected array $cache = [];

    protected int $defaultCacheExpiration = 3600;

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {
        $this->cache = [];

        return true;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->cache);
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): bool
    {
        if (isset($this->cache[$key])) {
            unset($this->cache[$key]);

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
    public function get(string $key, mixed $default = null): mixed
    {
        if (isset($this->cache[$key]) && $this->cache[$key]['expiry'] > time()) {
            return $this->cache[$key]['value'];
        }

        return $default;
    }

    /**
     * @inheritDoc
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $results = [];

        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int|DateInterval $ttl = null): bool
    {
        $this->cache[$key] = array(
            'value' => $value,
            'expiry' => time() + ($ttl ?? $this->defaultCacheExpiration),
        );

        return true;
    }

    /**
     * @inheritDoc
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }
}
