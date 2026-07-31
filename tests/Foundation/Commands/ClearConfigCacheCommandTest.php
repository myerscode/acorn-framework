<?php

declare(strict_types=1);

namespace Tests\Foundation\Commands;

use Myerscode\Acorn\Testing\Interactions\InteractsWithCommands;
use Tests\BaseTestCase;

final class ClearConfigCacheCommandTest extends BaseTestCase
{
    use InteractsWithCommands;

    public function testDeletesCacheFileWhenItExists(): void
    {
        $tempDir = self::createTempDirectory('config_cache');
        $cacheFile = $tempDir . '/config.php';
        file_put_contents($cacheFile, '<?php return [];');

        $this->assertTrue(file_exists($cacheFile));

        $container = $this->application()->container();
        $config = $container->get('config');
        $config->loadData(['cachedConfigLocation' => $cacheFile]);

        $this->call('config:clear');

        $this->assertFalse(file_exists($cacheFile));

        @rmdir($tempDir);
    }

    public function testHandlesNonExistentCacheFileGracefully(): void
    {
        $container = $this->application()->container();
        $config = $container->get('config');
        $config->loadData(['cachedConfigLocation' => '/tmp/nonexistent_' . time() . '.php']);

        // Should not throw
        $this->call('config:clear');
        $this->addToAssertionCount(1);
    }

    public function testHandlesNullCacheLocation(): void
    {
        $container = $this->application()->container();
        $config = $container->get('config');
        $config->loadData(['cachedConfigLocation' => null]);

        // Should not throw
        $this->call('config:clear');
        $this->addToAssertionCount(1);
    }
}
