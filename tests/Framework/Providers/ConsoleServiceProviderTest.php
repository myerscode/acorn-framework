<?php

namespace Tests\Framework\Providers;

use Myerscode\Acorn\Foundation\Console\Input;
use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Foundation\Providers\ConsoleProvider;
use Myerscode\Acorn\Testing\Interactions\InteractsWithContainer;
use Tests\BaseTestCase;

class ConsoleServiceProviderTest extends BaseTestCase
{
    use InteractsWithContainer;

    public function testServicesAreRegistered(): void
    {
        $consoleServiceProvider = new ConsoleProvider();

        $this->assertTrue($consoleServiceProvider->provides(Input::class));
        $this->assertTrue($consoleServiceProvider->provides('input'));
        $this->assertTrue($consoleServiceProvider->provides(Output::class));
        $this->assertTrue($consoleServiceProvider->provides('output'));
    }

    public function testContainerReturnsCorrectInstanceOfInput(): void
    {
        $container = $this->container();

        $instanceA = $container->get(Input::class);
        $instanceB = $container->get('input');
        $this->assertInstanceOf(Input::class, $instanceA);
        $this->assertInstanceOf(Input::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }

    public function testContainerReturnsCorrectInstanceOfOutput(): void
    {
        $container = $this->container();

        $instanceA = $container->get(Output::class);
        $instanceB = $container->get('output');
        $this->assertInstanceOf(Output::class, $instanceA);
        $this->assertInstanceOf(Output::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }


}
