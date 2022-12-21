<?php

namespace Tests\Foundation\Cache\Driver;

use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use Tests\BaseTestCase;

class FoundationDriverTest extends BaseTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testClear(DriverInterface $cache)
    {
        $cache->set('key', 'value', 3600);

        $this->assertGreaterThan(0, $cache->count());

        $cache->clear();

        $this->assertEquals(0, $cache->count());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete(DriverInterface $cache)
    {
        $cache->set('key', 'value');

        $this->assertEquals('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));

        $this->assertNull($cache->get('key'));

        $this->assertFalse($cache->delete('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteMultiple(DriverInterface $cache)
    {
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
    public function testGet(DriverInterface $cache)
    {
        $default = 'default';

        // Test when the key is not in the cache
        $result = $cache->get('key', $default);
        $this->assertEquals($default, $result);

        // Set a value in the cache that has expired
        $cache->set('key', 'value', -3600);
        $result = $cache->get('key', $default);
        $this->assertEquals($default, $result);

        // Set a value in the cache that has not expired
        $cache->set('key', 'value', 3600);
        $result = $cache->get('key', $default);
        $this->assertEquals('value', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetMultiple(DriverInterface $cache)
    {
        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');
        $values = $cache->getMultiple(['key1', 'key2', 'non-existent-key']);
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2', 'non-existent-key' => null], $values);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHas(DriverInterface $cache)
    {
        $this->assertFalse($cache->has('non-existent-key'));

        $cache->set('key', 'value');
        $this->assertTrue($cache->has('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSet(DriverInterface $cache)
    {
        // Set a value in the cache
        $cache->set('key', 'value');
        $this->assertEquals('value', $cache->get('key'));

        // Set a new value for the same key in the cache
        $cache->set('key', 'new value', 3600);
        $this->assertEquals('new value', $cache->get('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetMultiple(DriverInterface $cache)
    {
        $this->assertTrue($cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame('value1', $cache->get('key1'));
        $this->assertSame('value2', $cache->get('key2'));
    }

    protected function dataProvider(): array
    {
        return [
            RuntimeCache::class => [new RuntimeCache()],
            FileCacheDriver::class => [new FileCacheDriver($this->createTempDirectory('file_cache_testing'))],
        ];
    }
}
