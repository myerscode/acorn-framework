<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions\Handlers;

use Myerscode\Acorn\Foundation\Exceptions\Handlers\DefaultHandler;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;

final class DefaultHandlerTest extends BaseTestCase
{
    public function testFallsBackToClassNameForEmptyMessage(): void
    {
        $output = new BufferedOutput();

        new DefaultHandler()->handle(new RuntimeException(''), $output);

        $this->assertStringContainsString('RuntimeException', $output->fetch());
    }

    public function testNormalVerbosityOmitsFileAndLine(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL);

        new DefaultHandler()->handle(new RuntimeException('Arthur'), $output);

        $this->assertStringNotContainsString(__FILE__, $output->fetch());
    }
    public function testRendersMessageAndShortName(): void
    {
        $output = new BufferedOutput();

        $handled = new DefaultHandler()->handle(new RuntimeException('Rupert ran off'), $output);

        $this->assertTrue($handled);
        $content = $output->fetch();
        $this->assertStringContainsString('RuntimeException', $content);
        $this->assertStringContainsString('Rupert ran off', $content);
    }

    public function testVerboseAddsFileAndLine(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        new DefaultHandler()->handle(new RuntimeException('Gerald'), $output);
        $content = $output->fetch();

        $this->assertStringContainsString(__FILE__, $content);
    }

    public function testVeryVerboseIncludesTrace(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERY_VERBOSE);

        new DefaultHandler()->handle(new RuntimeException('Arthur'), $output);

        $this->assertStringContainsString('Exception trace:', $output->fetch());
    }
}
