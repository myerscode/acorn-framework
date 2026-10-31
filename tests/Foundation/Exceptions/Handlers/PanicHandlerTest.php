<?php

declare(strict_types=1);

namespace Tests\Foundation\Exceptions\Handlers;

use Myerscode\Acorn\Foundation\Exceptions\Handlers\PanicHandler;
use RuntimeException;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\BaseTestCase;

final class PanicHandlerTest extends BaseTestCase
{
    public function testFallsBackToGenericMessageWhenEmpty(): void
    {
        $output = new BufferedOutput();

        new PanicHandler()->handle(new RuntimeException(''), $output);

        $this->assertStringContainsString('fatal error', $output->fetch());
    }

    public function testNormalVerbositySuggestsVerboseFlag(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_NORMAL);

        new PanicHandler()->handle(new RuntimeException('Gerald'), $output);
        $content = $output->fetch();

        $this->assertStringContainsString('Run with -v', $content);
        $this->assertStringNotContainsString(__FILE__, $content);
    }
    public function testRendersPanicBannerAndMessage(): void
    {
        $output = new BufferedOutput();

        $handled = new PanicHandler()->handle(new RuntimeException('Rupert panicked'), $output);

        $this->assertTrue($handled);
        $content = $output->fetch();
        $this->assertStringContainsString('PANIC', $content);
        $this->assertStringContainsString('Rupert panicked', $content);
    }

    public function testVerboseShowsFileAndLineInsteadOfHint(): void
    {
        $output = new BufferedOutput(OutputInterface::VERBOSITY_VERBOSE);

        new PanicHandler()->handle(new RuntimeException('Arthur'), $output);
        $content = $output->fetch();

        $this->assertStringContainsString(__FILE__, $content);
        $this->assertStringNotContainsString('Run with -v', $content);
    }
}
