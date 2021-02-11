<?php

namespace Tests\Framework\Providers;

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Providers\ConsoleServiceProvider;
use Tests\BaseTestCase;

class ConsoleServiceProviderTest extends BaseTestCase
{

    public function testServicesAreRegistered()
    {
        $provider = new ConsoleServiceProvider();

        $this->assertTrue($provider->provides(Input::class));
        $this->assertTrue($provider->provides('input'));
        $this->assertTrue($provider->provides(Output::class));
        $this->assertTrue($provider->provides('output'));
    }

    public function testContainerReturnsCorrectInstanceOfInput()
    {
        $container = new Container();
        $instanceA = $container->manager()->get(Input::class);
        $instanceB = $container->manager()->get('input');
        $this->assertInstanceOf(Input::class, $instanceA);
        $this->assertInstanceOf(Input::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }

    public function testContainerReturnsCorrectInstanceOfOutput()
    {
        $container = new Container();
        $instanceA = $container->manager()->get(Output::class);
        $instanceB = $container->manager()->get('output');
        $this->assertInstanceOf(Output::class, $instanceA);
        $this->assertInstanceOf(Output::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }


}
