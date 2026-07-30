<?php

declare(strict_types=1);

namespace Tests\Foundation\Log;

use Myerscode\Acorn\Foundation\Log\Logger;
use Myerscode\Acorn\Foundation\Log\LogRotation;
use PHPUnit\Framework\TestCase;

final class LoggerTest extends TestCase
{
    private string $logDir;

    protected function setUp(): void
    {
        $this->logDir = sys_get_temp_dir() . '/acorn-logger-test';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir);
        }

        array_map(unlink(...), glob($this->logDir . '/*'));
    }

    public function testCustomLogPrefix(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Single, 'customprefix');
        $logger->info('Test custom prefix');

        $file = $this->logDir . '/customprefix.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test custom prefix', (string) file_get_contents($file));
    }

    public function testWritesToDailyFile(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Daily);
        $logger->info('Test daily file');

        $file = $this->logDir . '/acorn-' . date('Y-m-d') . '.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test daily file', (string) file_get_contents($file));
    }

    public function testWritesToMonthlyFile(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Monthly);
        $logger->info('Test monthly file');

        $file = $this->logDir . '/acorn-' . date('Y-m') . '.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test monthly file', (string) file_get_contents($file));
    }

    public function testWritesToSingleFile(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Single);
        $logger->info('Test single file');

        $file = $this->logDir . '/acorn.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test single file', (string) file_get_contents($file));
    }

    public function testCreatesDirectoryIfMissing(): void
    {
        $nestedDir = sys_get_temp_dir() . '/acorn-logger-test-nested-' . uniqid();
        $this->assertDirectoryDoesNotExist($nestedDir);

        $logger = new Logger($nestedDir . '/deep/path', LogRotation::Single);
        $logger->info('Test nested directory creation');

        $this->assertDirectoryExists($nestedDir . '/deep/path');
        $this->assertStringContainsString('Test nested directory creation', (string) file_get_contents($nestedDir . '/deep/path/acorn.log'));

        // cleanup
        array_map(unlink(...), glob($nestedDir . '/deep/path/*') ?: []);
        rmdir($nestedDir . '/deep/path');
        rmdir($nestedDir . '/deep');
        rmdir($nestedDir);
    }

    public function testWritesToWeeklyFile(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Weekly);
        $logger->info('Test weekly file');

        $file = $this->logDir . '/acorn-week-' . date('o-W') . '.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test weekly file', (string) file_get_contents($file));
    }

    public function testWritesToYearlyFile(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Yearly);
        $logger->info('Test yearly file');

        $file = $this->logDir . '/acorn-' . date('Y') . '.log';
        $this->assertFileExists($file);
        $this->assertStringContainsString('Test yearly file', (string) file_get_contents($file));
    }

    public function testSanitisesNewlinesInMessage(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Single);
        $logger->info("Line one\nLine two\r\nLine three");

        $file = $this->logDir . '/acorn.log';
        $contents = (string) file_get_contents($file);
        $lines = array_filter(explode("\n", trim($contents)));

        // Should be a single log entry, not multiple lines from injected newlines
        $this->assertCount(1, $lines);
        $this->assertStringContainsString('Line one Line two Line three', $contents);
    }

    public function testSanitisesNewlinesInContext(): void
    {
        $logger = new Logger($this->logDir, LogRotation::Single);
        $logger->info('Test', ['key' => "value\ninjected"]);

        $file = $this->logDir . '/acorn.log';
        $contents = (string) file_get_contents($file);
        $lines = array_filter(explode("\n", trim($contents)));

        $this->assertCount(1, $lines);
        $this->assertStringNotContainsString("\n" . 'injected', $contents);
    }
}
