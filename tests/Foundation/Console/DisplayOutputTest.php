<?php

declare(strict_types=1);

namespace Tests\Foundation\Console;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\BaseTestCase;

final class DisplayOutputTest extends BaseTestCase
{
    public function testJsonCompactWhenNotPretty(): void
    {
        $input = new StringInput('');
        $buffered = new BufferedOutput();
        $output = new DisplayOutput($input, $buffered);

        $output->json(['name' => 'Arthur'], false);

        $this->assertSame('{"name":"Arthur"}', trim($buffered->fetch()));
    }

    public function testJsonRendersPrettyByDefault(): void
    {
        $input = new StringInput('');
        $buffered = new BufferedOutput();
        $output = new DisplayOutput($input, $buffered);

        $output->json(['corgis' => ['Rupert', 'Gerald']]);

        $rendered = $buffered->fetch();

        // Valid JSON, pretty-printed (contains newlines/indentation).
        $this->assertSame(['corgis' => ['Rupert', 'Gerald']], json_decode(trim($rendered), true));
        $this->assertStringContainsString("\n", $rendered);
    }
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
