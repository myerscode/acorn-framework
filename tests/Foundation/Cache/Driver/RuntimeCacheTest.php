<?php

declare(strict_types=1);

namespace Tests\Foundation\Cache\Driver;

use DateInterval;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;
use Tests\BaseTestCase;
use ReflectionClass;

final class RuntimeCacheTest extends BaseTestCase
{
    public function testHasReturnsFalseForExpiredEntries(): void
    {
        $driver = new RuntimeCache();

        $driver->set('key', 'value', 9999);
        $this->assertTrue($driver->has('key'));

        // Overwrite with already-expired entry via reflection
        $reflection = new ReflectionClass($driver);
        $prop = $reflection->getProperty('cache');
        $prop->setValue($driver, [
            'key' => ['value' => 'value', 'expiry' => time() - 10],
        ]);

        $this->assertFalse($driver->has('key'));
    }

    public function testSetWithDateIntervalConvertsCorrectly(): void
    {
        $driver = new RuntimeCache();

        // 1 hour interval should keep the entry alive
        $driver->set('key', 'value', new DateInterval('PT1H'));

        $this->assertTrue($driver->has('key'));
        $this->assertSame('value', $driver->get('key'));
    }

    public function testSetWithDateIntervalOfDaysConvertsCorrectly(): void
    {
        $driver = new RuntimeCache();

        // 1 day = 86400 seconds, not 0
        $driver->set('key', 'value', new DateInterval('P1D'));

        $this->assertTrue($driver->has('key'));
        $this->assertSame('value', $driver->get('key'));
    }
}
