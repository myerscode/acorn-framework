<?php

namespace Tests\Framework\Providers;

use Myerscode\Acorn\Foundation\Console\Input\Input;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Providers\ConsoleProvider;
use Myerscode\Acorn\Testing\Interactions\InteractsWithContainer;
use Tests\BaseTestCase;

class ConsoleServiceProviderTest extends BaseTestCase
{
    use InteractsWithContainer;

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

        $instanceA = $container->get(DisplayOutput::class);
        $instanceB = $container->get('output');
        $this->assertInstanceOf(DisplayOutput::class, $instanceA);
        $this->assertInstanceOf(DisplayOutput::class, $instanceB);
        $this->assertEquals($instanceA, $instanceB);
    }

    public function testServicesAreRegistered(): void
    {
        $consoleProvider = new ConsoleProvider();

        $this->assertTrue($consoleProvider->provides(Input::class));
        $this->assertTrue($consoleProvider->provides('input'));
        $this->assertTrue($consoleProvider->provides(DisplayOutput::class));
        $this->assertTrue($consoleProvider->provides('output'));
    }


}
