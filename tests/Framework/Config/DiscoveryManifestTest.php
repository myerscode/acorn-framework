<?php

declare(strict_types=1);

namespace Tests\Framework\Config;

use Myerscode\Acorn\Framework\Config\DiscoveryManifest;
use Tests\BaseTestCase;

final class DiscoveryManifestTest extends BaseTestCase
{
    public function testReadFiltersNonStringEntries(): void
    {
        $path = $this->tempPath();
        mkdir(dirname($path), 0700, true);
        file_put_contents($path, json_encode(['commands' => ['Valid', 123, null], 'providers' => 'oops']));

        $manifest = new DiscoveryManifest($path)->read();

        $this->assertSame(['Valid'], $manifest['commands']);
        $this->assertSame([], $manifest['providers']);

        unlink($path);
    }

    public function testReadReturnsNullForMalformedFile(): void
    {
        $path = $this->tempPath();
        mkdir(dirname($path), 0700, true);
        file_put_contents($path, 'not json');

        $this->assertNull(new DiscoveryManifest($path)->read());

        unlink($path);
    }

    public function testReadReturnsNullWhenMissing(): void
    {
        $this->assertNull(new DiscoveryManifest($this->tempPath())->read());
    }

    public function testWriteThenReadRoundTrips(): void
    {
        $manifest = new DiscoveryManifest($this->tempPath());

        $this->assertFalse($manifest->exists());

        $manifest->write([
            'commands' => ['App\\Commands\\RupertCommand'],
            'providers' => ['App\\Providers\\GeraldProvider'],
            'listeners' => ['App\\Listeners\\ArthurListener'],
        ]);

        $this->assertTrue($manifest->exists());
        $this->assertSame([
            'commands' => ['App\\Commands\\RupertCommand'],
            'providers' => ['App\\Providers\\GeraldProvider'],
            'listeners' => ['App\\Listeners\\ArthurListener'],
        ], $manifest->read());

        $manifest->delete();
        $this->assertFalse($manifest->exists());
    }
    private function tempPath(): string
    {
        return sys_get_temp_dir() . '/acorn_manifest_' . uniqid() . '/manifest.json';
    }
}
