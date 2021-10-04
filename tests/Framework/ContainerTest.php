<?php

namespace Tests\Framework;

use Myerscode\Acorn\Container;
use Tests\BaseTestCase;

class ContainerTest extends BaseTestCase
{

    public function testContainerCanCreateItselfStatically()
    {
        $this->assertInstanceOf(Container::class, Container::getInstance());
    }

    public function testContainerCreatesItself()
    {
        $this->assertInstanceOf(Container::class, Container::getInstance());
    }

    public function testContainerCanFlushValues()
    {
        $c1 = Container::getInstance();
        Container::flush();
        $c2 = Container::getInstance();
        $this->assertNotSame($c1, $c2);
    }

    public function testContainerCanGetResolveValues()
    {
        $container = new Container();

        $container->manager()->add('testing', 'hello-world');
        $this->assertEquals('hello-world', $container->get('testing'));

        $container->add('foo', 'bar');
        $this->assertEquals('bar', $container->get('foo'));
    }
}
