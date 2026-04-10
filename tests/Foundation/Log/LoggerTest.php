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
}
