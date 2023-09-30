<?php

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\PackageDiscovery;
use Tests\BaseTestCase;

class PackageDiscoveryTest extends BaseTestCase
{
    protected string $appDirectory = 'tests/Mocks/DemoApp';

    public function testCanDiscoverAcornProvidersInPackages(): void
    {
        $packageDiscovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $packageDiscovery->locateProviders());
    }

    public function testCanDiscoverAcornCommandsInPackages(): void
    {
        $packageDiscovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $packageDiscovery->locateCommands());
    }
}
