<?php

declare(strict_types=1);

namespace Tests\Foundation\Commands;

use Myerscode\Acorn\Testing\Interactions\InteractsWithCommands;
use Tests\BaseTestCase;

final class ShowConfigCommandTest extends BaseTestCase
{
    use InteractsWithCommands;

    public function testListsConfigLocations(): void
    {
        $output = $this->call('config:show');

        $this->assertStringContainsString('Config Loaded From Locations', $output);
    }

    public function testShowsConfigValuesAndWarning(): void
    {
        $output = $this->call('config:show');

        // Flattened config keys are rendered, and the command warns that it
        // exposes filesystem paths.
        $this->assertStringContainsString('Key', $output);
        $this->assertStringContainsString('Value', $output);
        $this->assertStringContainsString('untrusted environments', $output);
    }
}
