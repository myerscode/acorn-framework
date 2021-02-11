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
        $container = new Container();

        $this->assertEquals($container, Container::getInstance());
    }

    public function testContainerCanGetInatances()
    {
        $container = new Container();

        $container->manager()->add('testing', 'hello-world');
        $this->assertEquals('hello-world', $container->get('testing'));
    }
}
