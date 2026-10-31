<?php

declare(strict_types=1);

namespace Tests\Framework\Cache;

use PHPUnit\Framework\Attributes\DataProvider;
use Iterator;
use Myerscode\Acorn\Foundation\Cache\Driver\FileCacheDriver;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;
use Myerscode\Acorn\Framework\Cache\Cache;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use Tests\BaseTestCase;
use DateTimeImmutable;

final class CacheTest extends BaseTestCase
{
    public static function dataProvider(): Iterator
    {
        yield RuntimeCache::class => [new RuntimeCache()];
        yield FileCacheDriver::class => [new FileCacheDriver(self::createTempDirectory('file_cache_testing'))];
    }

    #[DataProvider('dataProvider')]
    public function testAdd(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->add('key', 'value'));
        $this->assertFalse($cache->add('key', 'new value'));
    }

    #[DataProvider('dataProvider')]
    public function testDelete(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');

        $this->assertSame('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));

        $this->assertNull($cache->get('key'));
    }

    #[DataProvider('dataProvider')]
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

    #[DataProvider('dataProvider')]
    public function testDriver(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertInstanceOf(DriverInterface::class, $cache->driver());
        $this->assertEquals($driver, $cache->driver());
    }

    #[DataProvider('dataProvider')]
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

    #[DataProvider('dataProvider')]
    public function testForget(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');

        $this->assertSame('value', $cache->get('key'));

        $this->assertTrue($cache->delete('key'));

        $this->assertNull($cache->get('key'));

        $this->assertFalse($cache->delete('key'));
    }

    #[DataProvider('dataProvider')]
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

    #[DataProvider('dataProvider')]
    public function testGetMultiple(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key1', 'value1');
        $cache->set('key2', 'value2');

        $values = $cache->getMultiple(['key1', 'key2', 'non-existent-key']);

        $this->assertSame(['key1' => 'value1', 'key2' => 'value2', 'non-existent-key' => null], $values);
    }

    #[DataProvider('dataProvider')]
    public function testHas(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertFalse($cache->has('non-existent-key'));

        $cache->set('key', 'value');

        $this->assertTrue($cache->has('key'));
    }

    #[DataProvider('dataProvider')]
    public function testHasReturnsTrueForFalsyValues(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('false-key', false);
        $cache->set('zero-key', 0);
        $cache->set('empty-string-key', '');
        $cache->set('null-key', null);

        $this->assertTrue($cache->has('false-key'), 'has() should return true for cached false');
        $this->assertTrue($cache->has('zero-key'), 'has() should return true for cached 0');
        $this->assertTrue($cache->has('empty-string-key'), 'has() should return true for cached ""');
        $this->assertTrue($cache->has('null-key'), 'has() should return true for cached null');
        $this->assertFalse($cache->has('missing-key'), 'has() should return false for missing key');
    }

    #[DataProvider('dataProvider')]
    public function testIsMissing(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->isMissing('non-existent-key'));

        $cache->set('key', 'value');

        $this->assertFalse($cache->isMissing('key'));
    }

    #[DataProvider('dataProvider')]
    public function testIsMissingReturnsFalseForFalsyValues(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('false-key', false);
        $cache->set('zero-key', 0);
        $cache->set('empty-string-key', '');

        $this->assertFalse($cache->isMissing('false-key'), 'isMissing() should return false for cached false');
        $this->assertFalse($cache->isMissing('zero-key'), 'isMissing() should return false for cached 0');
        $this->assertFalse($cache->isMissing('empty-string-key'), 'isMissing() should return false for cached ""');
    }

    #[DataProvider('dataProvider')]
    public function testPull(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->set('key', 'value'));

        $this->assertSame('value', $cache->pull('key'));

        $this->assertNull($cache->pull('key'));
    }

    #[DataProvider('dataProvider')]
    public function testRemember(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertSame('value', $cache->remember('key', 1000, static fn (): string => 'value'));
        $this->assertSame('value', $cache->remember('key', 1000, static fn (): string => 'value2'));
    }

    #[DataProvider('dataProvider')]
    public function testRememberReturnsFalsyValueWithoutCallingCallbackAgain(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $calls = 0;
        $result = $cache->remember('key', 1000, static function () use (&$calls): bool {
            $calls++;
            return false;
        });

        $this->assertFalse($result);
        $this->assertSame(1, $calls, 'Callback should only be called once');

        // Second call should use cached value, not invoke callback again
        $result2 = $cache->remember('key', 1000, static function () use (&$calls): string {
            $calls++;
            return 'should not be called';
        });

        $this->assertFalse($result2);
        $this->assertSame(1, $calls, 'Callback should not be called again for cached false');
    }

    #[DataProvider('dataProvider')]
    public function testSet(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $cache->set('key', 'value');
        $this->assertSame('value', $cache->get('key'));

        $cache->set('key', 'value2');
        $this->assertSame('value2', $cache->get('key'));
    }

    #[DataProvider('dataProvider')]
    public function testSetMultiple(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->assertTrue($cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame('value1', $cache->get('key1'));
        $this->assertSame('value2', $cache->get('key2'));
    }

    #[DataProvider('dataProvider')]
    public function testSetMultipleNamespacesKeys(DriverInterface $driver): void
    {
        // With a namespace, setMultiple previously wrote un-namespaced keys
        // while get() read namespaced ones, so values were never found.
        $cache = new Cache($driver, 'ns:');

        $this->assertTrue($cache->setMultiple(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertSame('value1', $cache->get('key1'));
        $this->assertSame('value2', $cache->get('key2'));
        $this->assertTrue($cache->has('key1'));
    }

    #[DataProvider('dataProvider')]
    public function testSetMultipleWithDateTimeInterfaceTtl(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $future = new DateTimeImmutable('+1 hour');
        $this->assertTrue($cache->setMultiple(['key1' => 'value1'], $future));
        $this->assertSame('value1', $cache->get('key1'));
    }

    #[DataProvider('dataProvider')]
    public function testSetThrowsForPastDateTimeInterfaceTtl(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $this->expectException(\Myerscode\Acorn\Framework\Cache\Exception\InvalidArgumentException::class);

        $past = new DateTimeImmutable('-1 hour');
        $cache->set('key', 'value', $past);
    }

    #[DataProvider('dataProvider')]
    public function testSetWithDateTimeInterfaceTtl(DriverInterface $driver): void
    {
        $cache = $this->cacheProvider($driver);

        $future = new DateTimeImmutable('+1 hour');
        $cache->set('key', 'value', $future);

        $this->assertTrue($cache->has('key'));
        $this->assertSame('value', $cache->get('key'));
    }

    protected function cacheProvider(DriverInterface $driver): Cache
    {
        return (new Cache($driver));
    }
}
