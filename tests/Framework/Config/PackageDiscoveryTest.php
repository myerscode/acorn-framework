<?php

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\PackageDiscovery;
use Tests\BaseTestCase;

class PackageDiscoveryTest extends BaseTestCase
{
    protected string $appDirectory = 'tests/Mocks/DemoApp';

    public function testCanDiscoverAcornProvidersInPackages(): void
    {
        $discovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $discovery->locateProviders());
    }

    public function testCanDiscoverAcornCommandsInPackages(): void
    {
        $discovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $discovery->locateCommands());
    }
}
