<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Cache\Driver;

use DateInterval;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use DateTimeImmutable;

class FileCacheDriver implements DriverInterface
{
    public function __construct(protected readonly string $cacheDir)
    {
        if (!is_dir($this->cacheDir)) {
            // 0700: cache entries may hold sensitive application state and
            // the file names are predictable (md5 of the key), so keep the
            // directory private to the owner.
            mkdir($this->cacheDir, 0700, true);
        }
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
        $cacheFiles = glob($this->cacheDir . '/*') ?: [];
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
        $cacheFiles = glob($this->cacheDir . '/*') ?: [];

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
     *
     * @param iterable<string> $keys
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
        $cacheFile = $this->getCacheFile($key);
        if (!file_exists($cacheFile)) {
            return $default;
        }

        $data = $this->readEntry($cacheFile);
        if ($data === null) {
            return $default;
        }

        if (time() > $data['expiration']) {
            // Cache is expired, delete it and return the default value
            unlink($cacheFile);

            return $default;
        }

        return $data['value'];
    }

    /**
     * @inheritDoc
     *
     * @param iterable<string> $keys
     * @return array<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
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

        $data = $this->readEntry($cacheFile);
        if ($data === null) {
            return false;
        }

        if (time() > $data['expiration']) {
            unlink($cacheFile);

            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int|DateInterval|null $ttl = null): bool
    {
        $cacheFile = $this->getCacheFile($key);

        $ttlSeconds = $ttl instanceof DateInterval ? $this->intervalToSeconds($ttl) : ($ttl ?? PHP_INT_MAX);

        $data = [
            'value' => $value,
            'expiration' => $ttl !== null ? time() + $ttlSeconds : PHP_INT_MAX,
        ];

        file_put_contents($cacheFile, json_encode($data));

        return true;
    }

    /**
     * @inheritDoc
     *
     * @param iterable<string, mixed> $values
     */
    public function setMultiple(iterable $values, int|DateInterval|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $this->set((string) $key, $value, $ttl);
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

    /**
     * Convert a DateInterval to total seconds.
     */
    private function intervalToSeconds(DateInterval $interval): int
    {
        $reference = new DateTimeImmutable();
        $endTime = $reference->add($interval);

        return $endTime->getTimestamp() - $reference->getTimestamp();
    }

    /**
     * Read and validate a cache entry from disk.
     *
     * Returns null for a missing, unreadable, non-JSON, or malformed
     * file rather than trusting its shape; a truncated or externally
     * written entry must never trigger an undefined-key error or let a
     * partial payload masquerade as a live value.
     *
     * @return array{value: mixed, expiration: int}|null
     */
    private function readEntry(string $cacheFile): ?array
    {
        if (!file_exists($cacheFile)) {
            return null;
        }

        $content = file_get_contents($cacheFile);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data) || !array_key_exists('value', $data) || !isset($data['expiration']) || !is_int($data['expiration'])) {
            return null;
        }

        return $data;
    }
}
