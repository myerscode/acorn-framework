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
}
