<?php

declare(strict_types=1);

namespace Tests\Foundation\Cache\Driver;

use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\BaseTestCase;
use DateInterval;

final class FileCacheDriverTest extends BaseTestCase
{
    /**
     * A truncated, non-JSON, or externally written entry must not trigger an
     * undefined-key error; get() returns the default and has() returns false.
     *
     * @return array<string, array{0: string}>
     */
    public static function malformedEntryProvider(): array
    {
        return [
            'not json' => ['this is not json at all'],
            'truncated json' => ['{"value":"Rupert","expir'],
            'missing expiration' => ['{"value":"Gerald"}'],
            'missing value' => ['{"expiration":9999999999}'],
            'non-int expiration' => ['{"value":"Arthur","expiration":"soon"}'],
            'json scalar not array' => ['42'],
            'empty file' => [''],
        ];
    }
    public function testCanGetCacheDir(): void
    {
        $cacheDir = $this->createTempDirectory('file_cache_testing');

        $fileCacheDriver = new FileCacheDriver($cacheDir);

        $this->assertSame($cacheDir, $fileCacheDriver->cacheDir());
    }

    public function testConstructorCreatesDirectoryIfMissing(): void
    {
        $baseDir = sys_get_temp_dir() . '/file_cache_mkdir_test_' . time();
        $cacheDir = $baseDir . '/nested/cache';

        $this->assertDirectoryDoesNotExist($cacheDir);

        new FileCacheDriver($cacheDir);

        $this->assertDirectoryExists($cacheDir);

        // cleanup
        @rmdir($cacheDir);
        @rmdir($baseDir . '/nested');
        @rmdir($baseDir);
    }

    public function testConstructorCreatesPrivateCacheDirectory(): void
    {
        $cacheDir = sys_get_temp_dir() . '/file_cache_perms_' . uniqid();

        new FileCacheDriver($cacheDir);

        $this->assertDirectoryExists($cacheDir);
        // Directory is owner-only (0700); mask off the file-type bits.
        $this->assertSame('0700', substr(sprintf('%o', fileperms($cacheDir)), -4));

        @rmdir($cacheDir);
    }

    #[DataProvider('malformedEntryProvider')]
    public function testGetReturnsDefaultForMalformedEntry(string $rawContent): void
    {
        $cacheDir = $this->createTempDirectory('file_cache_malformed');
        $driver = new FileCacheDriver($cacheDir);

        $cacheFile = $cacheDir . '/' . md5('corgi') . '.cache';
        file_put_contents($cacheFile, $rawContent);

        $this->assertSame('default', $driver->get('corgi', 'default'));
        $this->assertFalse($driver->has('corgi'));
    }

    public function testHasReturnsFalseForExpiredEntries(): void
    {
        $cacheDir = $this->createTempDirectory('file_cache_expiry');
        $driver = new FileCacheDriver($cacheDir);

        $driver->set('key', 'value', 1);
        $this->assertTrue($driver->has('key'));

        // Manually expire the entry by overwriting the file
        $cacheFile = $cacheDir . '/' . md5('key') . '.cache';
        $data = ['value' => 'value', 'expiration' => time() - 10];
        file_put_contents($cacheFile, json_encode($data));

        $this->assertFalse($driver->has('key'));
    }

    public function testSetWithDateIntervalConvertsCorrectly(): void
    {
        $cacheDir = $this->createTempDirectory('file_cache_interval');
        $driver = new FileCacheDriver($cacheDir);

        // 1 hour interval should result in ~3600 seconds, not 0
        $driver->set('key', 'value', new DateInterval('PT1H'));

        $this->assertTrue($driver->has('key'));
        $this->assertSame('value', $driver->get('key'));
    }
}
