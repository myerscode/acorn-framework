<?php

namespace Tests\Framework\Cache;

use Iterator;
use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;
use Myerscode\Acorn\Framework\Cache\Cache;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use Tests\BaseTestCase;

class CacheTest extends BaseTestCase
{
    public static function dataProvider(): Iterator
    {
        yield RuntimeCache::class => [new RuntimeCache()];
        yield FileCacheDriver::class => [new FileCacheDriver(self::createTempDirectory('file_cache_testing'))];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testAdd(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->add('key', 'value'));
        $this->assertFalse($cache->add('key', 'new value'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');

        $this->assertSame('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));

        $this->assertNull($cache->get('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteMultiple(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');
        $cache->set('key3', 'value3');
        $this->assertTrue($cache->deleteMultiple(['key1', 'key2']));
        $this->assertNull($cache->get('key1'));
        $this->assertNull($cache->get('key2'));
        $this->assertNotNull($cache->get('key3'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDriver(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertInstanceOf(DriverInterface::class, $cache->driver());
        $this->assertEquals($driver, $cache->driver());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testFlush(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');
        $cache->set('key3', 'value3');

        $this->assertGreaterThan(0, $cache->count());

        $cache->flush();

        $this->assertSame(0, $cache->count());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testForget(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');

        $this->assertSame('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));

        $this->assertNull($cache->get('key'));

        $this->assertFalse($cache->delete('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGet(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $default = 'default';

        // Test when the key is not in the cache
        $result = $cache->get('key', $default);
        $this->assertSame($default, $result);

        // Set a value in the cache that has expired
        $cache->set('key', 'value', -3600);
        $result = $cache->get('key', $default);
        $this->assertSame($default, $result);

        // Set a value in the cache that has not expired
        $cache->set('key', 'value', 3600);
        $result = $cache->get('key', $default);
        $this->assertSame('value', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetMultiple(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $values = $cache->getMultiple(['key1', 'key2', 'non-existent-key']);

        $this->assertSame(['key1' => 'value1', 'key2' => 'value2', 'non-existent-key' => null], $values);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHas(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertFalse($cache->has('non-existent-key'));

        $cache->set('key', 'value');

        $this->assertTrue($cache->has('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testIsMissing(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->isMissing('non-existent-key'));

        $cache->set('key', 'value');

        $this->assertFalse($cache->isMissing('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testPull(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->set('key', 'value'));

        $this->assertSame('value', $cache->pull('key'));

        $this->assertNull($cache->pull('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testRemember(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertSame('value', $cache->remember('key', 1000, static fn(): string => 'value'));
        $this->assertSame('value', $cache->remember('key', 1000, static fn(): string => 'value2'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSet(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');
        $this->assertSame('value', $cache->get('key'));

        $cache->set('key', 'value2');
        $this->assertSame('value2', $cache->get('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetMultiple(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame('value1', $cache->get('key1'));
        $this->assertSame('value2', $cache->get('key2'));
    }

    protected function cacheProvider(DriverInterface $driver): Cache
    {
        return (new Cache($driver));
    }
}
