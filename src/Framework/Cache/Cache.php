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
        if ($ttl instanceof DateTimeInterface) {
            $seconds = $ttl->getTimestamp() - time();
            if ($seconds <= 0) {
                throw new InvalidArgumentException(
                    sprintf('DateTimeInterface TTL must be in the future, got %s', $ttl->format('Y-m-d H:i:s')),
                );
            }
            $ttl = $seconds;
        }

        return $this->driver->set($this->namespacedKey($key), $value, $ttl);
    }

    /**
     * @param iterable<string, mixed> $values
     */
    public function setMultiple(iterable $values, DateInterval|DateTimeInterface|int|null $ttl = null): bool
    {
        $success = true;

        foreach ($values as $key => $value) {
            // Delegate to set() so keys are namespaced and a DateTimeInterface
            // TTL is normalised identically to a single set(); writing through
            // the driver directly skipped both and left values unreadable.
            $success = $this->set((string) $key, $value, $ttl) && $success;
        }

        return $success;
    }

    protected function namespacedKey(string $key): string
    {
        $this->validateKey($key);

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

    /**
     * Validate a cache key against the PSR-16 rules: a non-empty string that
     * does not contain any of the reserved characters {}()/\@:.
     *
     * @throws InvalidArgumentException
     */
    protected function validateKey(string $key): void
    {
        if ($key === '') {
            throw new InvalidArgumentException('Cache key must not be an empty string.');
        }

        if (preg_match('#[{}()/\\\\@:]#', $key) === 1) {
            throw new InvalidArgumentException(
                sprintf('Cache key "%s" contains one or more reserved characters {}()/\\@:', $key),
            );
        }
    }
}
