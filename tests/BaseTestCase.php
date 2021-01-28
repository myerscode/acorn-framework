<?php

namespace Tests;

use Mockery;
use Myerscode\Acorn\Framework\Events\CallableEventManager;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function mock($class, $constructorArgs = [])
    {
        return Mockery::mock($class, $constructorArgs);
    }

    public function stub($class)
    {
        return Mockery::mock($class);
    }

    protected function setUp(): void
    {
        CallableEventManager::clear();
    }
}
