<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Log;

use Myerscode\Acorn\Foundation\Log\LogRotation;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Logger implements LoggerInterface
{
    protected string $logDirectory;

    protected string $logFileName;

    public function __construct(string $logDirectory, protected LogRotation $rotation = LogRotation::Single, protected string $logPrefix = 'acorn')
    {
        $this->logDirectory = rtrim($logDirectory, '/');
        $this->logFileName = $this->getLogFileName();
    }

    protected function getLogFileName(): string
    {
        $base = $this->logDirectory . '/' . $this->logPrefix;
        switch ($this->rotation) {
            case LogRotation::Daily:
                return $base . '-' . date('Y-m-d') . '.log';
            case LogRotation::Weekly:
                return $base . '-week-' . date('o-W') . '.log';
            case LogRotation::Monthly:
                return $base . '-' . date('Y-m') . '.log';
            case LogRotation::Yearly:
                return $base . '-' . date('Y') . '.log';
            case LogRotation::Single:
            default:
                return $base . '.log';
        }
    }

    public function log($level, $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = $context !== [] ? json_encode($context) : '';
        $entry = sprintf('[%s] [%s] %s', $date, $level, $message) . ($contextStr ? ' ' . $contextStr : '') . "\n";
        file_put_contents($this->getLogFileName(), $entry, FILE_APPEND);
    }

    public function emergency($message, array $context = []): void { $this->log(LogLevel::EMERGENCY, $message, $context); }

    public function alert($message, array $context = []): void { $this->log(LogLevel::ALERT, $message, $context); }

    public function critical($message, array $context = []): void { $this->log(LogLevel::CRITICAL, $message, $context); }

    public function error($message, array $context = []): void { $this->log(LogLevel::ERROR, $message, $context); }

    public function warning($message, array $context = []): void { $this->log(LogLevel::WARNING, $message, $context); }

    public function notice($message, array $context = []): void { $this->log(LogLevel::NOTICE, $message, $context); }

    public function info($message, array $context = []): void { $this->log(LogLevel::INFO, $message, $context); }

    public function debug($message, array $context = []): void { $this->log(LogLevel::DEBUG, $message, $context); }
}
