<?php

declare(strict_types=1);

namespace Tests\Foundation\Cache\Driver;

use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Tests\BaseTestCase;

final class FileCacheDriverTest extends BaseTestCase
{
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
}
