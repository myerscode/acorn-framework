<?php

namespace Tests\Foundation\Cache\Driver;

use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Tests\BaseTestCase;

class FileCacheDriverTest extends BaseTestCase
{
    public function testCanGetCacheDir(): void
    {
        $cacheDir = $this->createTempDirectory('file_cache_testing');

        $fileCacheDriver = new FileCacheDriver($cacheDir);

        $this->assertSame($cacheDir, $fileCacheDriver->cacheDir());
    }
}
