<?php

namespace Tests\Framework\Providers;

use Myerscode\Acorn\Foundation\Providers\CacheProvider;
use Myerscode\Acorn\Framework\Cache\Cache;
use Myerscode\Acorn\Testing\Interactions\InteractsWithContainer;
use Tests\BaseTestCase;

class CacheServiceProviderTest extends BaseTestCase
{
    use InteractsWithContainer;

    public function testContainerReturnsInstanceOfCache(): void
    {
        $container = $this->container();

        $instanceA = $container->get(Cache::class);
        $instanceB = $container->get('cache');
        $this->assertInstanceOf(Cache::class, $instanceA);
        $this->assertInstanceOf(Cache::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }

    public function testServicesAreRegistered(): void
    {
        $cacheProvider = new CacheProvider();

        $this->assertTrue($cacheProvider->provides(Cache::class));
        $this->assertTrue($cacheProvider->provides('cache'));
    }
}
