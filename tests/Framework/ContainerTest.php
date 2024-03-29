<?php

namespace Tests\Framework;

use Myerscode\Acorn\Framework\Container\Container;
use Tests\BaseTestCase;

class ContainerTest extends BaseTestCase
{
    public function testContainerCanCreateItselfStatically(): void
    {
        $this->assertInstanceOf(Container::class, Container::getInstance());
    }

    public function testContainerCreatesItself(): void
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
}
