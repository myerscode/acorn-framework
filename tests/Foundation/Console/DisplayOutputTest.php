<?php

declare(strict_types=1);

namespace Tests\Foundation\Console;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\BaseTestCase;

final class DisplayOutputTest extends BaseTestCase
{
    public function testTableRendersOutput(): void
    {
        $input = new StringInput('');
        $buffered = new BufferedOutput();
        $output = new DisplayOutput($input, $buffered);

        $output->table(['Name', 'Role'], [['Alice', 'Admin'], ['Bob', 'User']]);

        $rendered = $buffered->fetch();

        $this->assertStringContainsString('Alice', $rendered);
        $this->assertStringContainsString('Bob', $rendered);
        $this->assertStringContainsString('Name', $rendered);
        $this->assertStringContainsString('Role', $rendered);
    }

    public function testTableWithEmptyDataRendersHeaders(): void
    {
        $input = new StringInput('');
        $buffered = new BufferedOutput();
        $output = new DisplayOutput($input, $buffered);

        $output->table(['ID', 'Value'], []);

        $rendered = $buffered->fetch();

        $this->assertStringContainsString('ID', $rendered);
        $this->assertStringContainsString('Value', $rendered);
    }
}
