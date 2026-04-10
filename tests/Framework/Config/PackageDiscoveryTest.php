<?php

declare(strict_types=1);

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\PackageDiscovery;
use Tests\BaseTestCase;
use Override;

final class PackageDiscoveryTest extends BaseTestCase
{
    #[Override]
    protected string $appDirectory = 'tests/Mocks/DemoApp';

    public function testCanDiscoverAcornCommandsInPackages(): void
    {
        $packageDiscovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $packageDiscovery->locateCommands());
    }

    public function testCanDiscoverAcornProvidersInPackages(): void
    {
        $packageDiscovery = new PackageDiscovery($this->appBase());

        $this->assertCount(2, $packageDiscovery->locateProviders());
    }
}
