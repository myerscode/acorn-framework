<?php

declare(strict_types=1);

namespace Tests\Framework;

use Myerscode\Acorn\Framework\Container\Container;
use Tests\BaseTestCase;

final class ContainerTest extends BaseTestCase
{
    public function testContainerCanCreateItselfStatically(): void
    {
        $this->assertInstanceOf(Container::class, Container::getInstance());
    }

    public function testContainerCanFlushValues(): void
    {
        $container = Container::getInstance();
        Container::flush();
        $c2 = Container::getInstance();
        $this->assertNotSame($container, $c2);
    }

    public function testContainerCanGetResolveValues(): void
    {
        $container = new Container();

        $container->add('testing', 'hello-world');
        $this->assertSame('hello-world', $container->get('testing'));

        $container->add('foo', 'bar');
        $this->assertSame('bar', $container->get('foo'));
    }

    public function testContainerCreatesItself(): void
    {
        $this->assertInstanceOf(Container::class, Container::getInstance());
    }

    public function testAddWithSharedFalseReturnsNewInstanceEachTime(): void
    {
        $container = new Container();

        $container->add(\stdClass::class, \stdClass::class, false);

        $first = $container->get(\stdClass::class);
        $second = $container->get(\stdClass::class);

        $this->assertNotSame($first, $second);
    }

    public function testAddWithSharedTrueReturnsSameInstance(): void
    {
        $container = new Container();

        $container->add(\stdClass::class, \stdClass::class, true);

        $first = $container->get(\stdClass::class);
        $second = $container->get(\stdClass::class);

        $this->assertSame($first, $second);
    }
}
