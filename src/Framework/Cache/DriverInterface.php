<?php

namespace Myerscode\Acorn\Framework\Cache;

interface DriverInterface
{
    /**
     * Retrieve an item from the cache by key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Store an item in the cache for a given number of seconds.
     */
    public function set(string $key, mixed $value, int $seconds): bool;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Remove all items from the cache.
     */
    public function flush(): bool;
}
