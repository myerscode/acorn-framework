<?php

declare(strict_types=1);

namespace Tests\Foundation\Commands;

use Myerscode\Acorn\Testing\Interactions\InteractsWithCommands;
use Tests\BaseTestCase;

final class ShowListenersCommandTest extends BaseTestCase
{
    use InteractsWithCommands;

    public function testListsListenerDirectorySources(): void
    {
        $output = $this->call('listener:show');

        $this->assertStringContainsString('Event Listener Directories', $output);
        $this->assertStringContainsString('app', $output);
        $this->assertStringContainsString('framework', $output);
        $this->assertStringContainsString('executing', $output);
    }
}
