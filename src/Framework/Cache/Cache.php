<?php

namespace Myerscode\Acorn\Framework\Cache;

use Closure;
use DateInterval;
use DateTimeInterface;
use Myerscode\Acorn\Framework\Cache\Exception\InvalidArgumentException;
use Myerscode\Utilities\Bags\Utility as Bag;

class Cache implements CacheInterface
{
    public function __construct(protected readonly DriverInterface $driver, protected readonly string $namespace = '')
    {
        //
    }

    /**
     * Store an item in the cache if the key does not exist.
     *
     * @see set
     */
    public function add(string $key, mixed $value, int|DateInterval|null $ttl = null): bool
    {
        if ($this->has($key)) {
            return false;
        }

        return $this->set($key, $value, $ttl);
    }

    public function count(): int
    {
        return $this->driver->count();
    }

    public function delete(string $key): bool
    {
        return $this->driver->delete($this->namespacedKey($key));
    }

    /**
     * @param iterable<string> $keys
     */
    public function deleteMultiple(iterable $keys): bool
    {
        return $this->driver->deleteMultiple($this->namespacedKeys($keys));
    }

    public function driver(): DriverInterface
    {
        return $this->driver;
    }

    public function flush(): void
    {
        $this->driver->clear();
    }

    /**
     * Remove an item from the cache.
     *
     * @see delete
     */
    public function forget(string $key): bool
    {
        return $this->delete($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->driver->get($this->namespacedKey($key), $default);
    }

    /**
     * @param iterable<string> $keys
     * @return iterable<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        return $this->driver->getMultiple($this->namespacedKeys($keys), $default);
    }

    public function has(string $key): bool
    {
        return $this->driver->has($this->namespacedKey($key));
    }

    public function isMissing(string $key): bool
    {
        return !$this->has($key);
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->forget($key);

        return $value;
    }

    public function remember(string $key, DateTimeInterface|DateInterval|int|null $ttl, Closure $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, DateInterval|DateTimeInterface|int|null $ttl = null): bool
    {
        $normalizedTtl = $ttl instanceof DateTimeInterface ? null : $ttl;

        return $this->driver->set($this->namespacedKey($key), $value, $normalizedTtl);
    }

    /**
     * @param iterable<string, mixed> $values
     */
    public function setMultiple(iterable $values, DateInterval|int|null $ttl = null): bool
    {
        return $this->driver->setMultiple($values, $ttl);
    }

    protected function namespacedKey(string $key): string
    {
        return $this->namespace.$key;
    }

    /**
     * @param iterable<string> $keys
     * @return array<string, string>
     */
    protected function namespacedKeys(iterable $keys): array
    {
        return new Bag($keys)->map(fn (string $key): string => $this->namespacedKey($key))->value();
    }
}
