<?php

namespace Tests\Foundation\Cache\Driver;

use Iterator;
use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use Tests\BaseTestCase;

class DriverTest extends BaseTestCase
{
    public static function dataProvider(): Iterator
    {
        yield RuntimeCache::class => [new RuntimeCache()];
        yield FileCacheDriver::class => [new FileCacheDriver(self::createTempDirectory('file_cache_testing'))];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testClear(DriverInterface $driver): void
    {
        $driver->set('key', 'value', 3600);

        $this->assertGreaterThan(0, $driver->count());

        $driver->clear();

        $this->assertSame(0, $driver->count());
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDelete(DriverInterface $driver): void
    {
        $driver->set('key', 'value');

        $this->assertSame('value', $driver->get('key'));

        $this->assertTrue($driver->delete('key'));

        $this->assertNull($driver->get('key'));

        $this->assertFalse($driver->delete('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testDeleteMultiple(DriverInterface $driver): void
    {
        $driver->set('key1', 'value1');
        $driver->set('key2', 'value2');
        $driver->set('key3', 'value3');
        $this->assertTrue($driver->deleteMultiple(['key1', 'key2']));
        $this->assertNull($driver->get('key1'));
        $this->assertNull($driver->get('key2'));
        $this->assertNotNull($driver->get('key3'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGet(DriverInterface $driver): void
    {
        $default = 'default';

        // Test when the key is not in the cache
        $result = $driver->get('key', $default);
        $this->assertSame($default, $result);

        // Set a value in the cache that has expired
        $driver->set('key', 'value', -3600);
        $result = $driver->get('key', $default);
        $this->assertSame($default, $result);

        // Set a value in the cache that has not expired
        $driver->set('key', 'value', 3600);
        $result = $driver->get('key', $default);
        $this->assertSame('value', $result);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetMultiple(DriverInterface $driver): void
    {
        $driver->set('key1', 'value1');
        $driver->set('key2', 'value2');

        $values = $driver->getMultiple(['key1', 'key2', 'non-existent-key']);
        $this->assertSame(['key1' => 'value1', 'key2' => 'value2', 'non-existent-key' => null], $values);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testHas(DriverInterface $driver): void
    {
        $this->assertFalse($driver->has('non-existent-key'));

        $driver->set('key', 'value');
        $this->assertTrue($driver->has('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSet(DriverInterface $driver): void
    {
        // Set a value in the cache
        $driver->set('key', 'value');
        $this->assertSame('value', $driver->get('key'));

        // Set a new value for the same key in the cache
        $driver->set('key', 'new value', 3600);
        $this->assertSame('new value', $driver->get('key'));
    }

    /**
     * @dataProvider dataProvider
     */
    public function testSetMultiple(DriverInterface $driver): void
    {
        $this->assertTrue($driver->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame('value1', $driver->get('key1'));
        $this->assertSame('value2', $driver->get('key2'));
    }
}
