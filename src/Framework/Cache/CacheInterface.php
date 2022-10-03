<?php

namespace Myerscode\Acorn\Framework\Cache;

use Psr\SimpleCache\CacheInterface as RepositoryInterface;
use Closure;
use DateTimeInterface;
use DateInterval;

interface CacheInterface extends RepositoryInterface
{
    /**
     * Retrieve an item from the cache and delete it.
     */
    public function pull(string $key, mixed $default = null): mixed;

    /**
     * Store an item in the cache, even if value exists.
     */
    public function put(string $key, mixed $value, DateInterval|DateTimeInterface|int|null $ttl = null): bool;

    /**
     * Get an item from the cache, or execute the given Closure and store the result then return the new value.
     */
    public function remember(string $key, DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;

    /**
     * Determine if an item exists in the cache.
     */
    public function has(string $key): bool;

    /**
     * Determine if an item does not exist in the cache.
     */
    public function isMissing(string $key): bool;
}
