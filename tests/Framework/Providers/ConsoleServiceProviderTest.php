<?php

namespace Tests\Framework\Providers;

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Foundation\Console\Input;
use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Framework\Providers\ConsoleServiceProvider;
use Tests\BaseTestCase;

class ConsoleServiceProviderTest extends BaseTestCase
{

    public function testServicesAreRegistered(): void
    {
        $consoleServiceProvider = null;
        $consoleServiceProvider = new ConsoleServiceProvider();

        $this->assertTrue($consoleServiceProvider->provides(Input::class));
        $this->assertTrue($consoleServiceProvider->provides('input'));
        $this->assertTrue($consoleServiceProvider->provides(Output::class));
        $this->assertTrue($consoleServiceProvider->provides('output'));
    }

    public function testContainerReturnsCorrectInstanceOfInput(): void
    {
        $container = new Container();
        $instanceA = $container->manager()->get(Input::class);
        $instanceB = $container->manager()->get('input');
        $this->assertInstanceOf(Input::class, $instanceA);
        $this->assertInstanceOf(Input::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }

    public function testContainerReturnsCorrectInstanceOfOutput(): void
    {
        $container = new Container();
        $instanceA = $container->manager()->get(Output::class);
        $instanceB = $container->manager()->get('output');
        $this->assertInstanceOf(Output::class, $instanceA);
        $this->assertInstanceOf(Output::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }


}
