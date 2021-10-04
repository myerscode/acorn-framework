<?php

namespace Tests;

use Mockery;
use Myerscode\Acorn\Container;
use Myerscode\Acorn\Framework\Config\Factory as ConfigFactory;
use Myerscode\Acorn\Framework\Events\CallableEventManager;
use PHPUnit\Framework\TestCase;

class BaseTestCase extends TestCase
{
    public function mock($class, $constructorArgs = [])
    {
        return Mockery::mock($class, $constructorArgs);
    }

    public function spy($class, $constructorArgs = [])
    {
        return Mockery::spy($class, $constructorArgs);
    }

    public function stub($class)
    {
        return Mockery::mock($class);
    }

    protected function setUp(): void
    {
        CallableEventManager::clear();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        Container::flush();
        Mockery::close();
    }

    public function resourceFilePath($fileName = ''): string
    {
        return __DIR__ . $fileName;
    }

    public function container(): Container
    {
        $container = new Container;
        $container->add('config', ConfigFactory::make([
            'base' => $this->resourceFilePath('/Resources/App'),
            'src' => dirname(__DIR__) . '/src',
            'cwd' => getcwd(),
        ]));

        return $container;
    }


    public function catch($e)
    {
        return new class ($e) {
            private $e;

            public function __construct($e)
            {
                $this->e = $e;
            }

            public function from(\Closure $c)
            {
                try {
                    return $c();
                } catch (\Exception $exception) {
                    if (!($exception instanceof $this->e)) {
                        throw $exception;
                    }
                }
            }
        };
    }
}
