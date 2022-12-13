<?php

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\Manager;
use Myerscode\Config\Config;
use Tests\BaseTestCase;

class ManagerTest extends BaseTestCase
{
    public function testReturnsConfigObject(): void
    {
        $manager = new Manager($this->appBase());

        $this->assertInstanceOf(Config::class, $manager->doNotCacheConfig()->loadConfig([], []));
    }

    public function testShouldCacheConfig(): void
    {
        $manager = new Manager($this->appBase());

        $manager->shouldCacheConfig();

        $this->assertTrue($manager->isCachingConfig());

        $manager->doNotCacheConfig();

        $this->assertFalse($manager->isCachingConfig());
    }

    public function testShouldIgnoreCache(): void
    {
        $manager = new Manager($this->appBase());

        $manager->shouldIgnoreCache();

        $this->assertTrue($manager-> isIgnoringCache());

        $manager->doNotIgnoreCache();

        $this->assertFalse($manager-> isIgnoringCache());
    }

    public function testCachingConfig(): void
    {
        $cacheLocation = __DIR__ . DIRECTORY_SEPARATOR;

        $cacheFile = __DIR__ . DIRECTORY_SEPARATOR . '.acorn/config.php';

        $manager = new Manager($cacheLocation);

        $manager->shouldCacheConfig();

        $this->assertFalse($manager->isUsingCachedConfig());

        $manager->loadConfig([], []);

        $this->assertFileExists($cacheFile);

        $manager->loadConfig([], []);

        $this->assertTrue($manager->isUsingCachedConfig());

        // cleanup cached file
        system('rm -rf -- ' . escapeshellarg($cacheLocation . '.acorn'));
    }
}
