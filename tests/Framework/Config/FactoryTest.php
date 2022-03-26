<?php

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\Factory;
use Myerscode\Config\Config;
use Tests\BaseTestCase;

class FactoryTest extends BaseTestCase
{

    public function testReturnsConfigObject(): void
    {
        $this->assertInstanceOf(Config::class, Factory::make([]));
    }
}
