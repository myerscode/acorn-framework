<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Log;

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

    public function alert(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function debug(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function emergency(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function error(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function info(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function log(mixed $level, mixed $message, array $context = []): void
    {
        if (!is_dir($this->logDirectory)) {
            mkdir($this->logDirectory, 0755, true);
        }

        $date = date('Y-m-d H:i:s');
        $sanitisedMessage = str_replace(["\r\n", "\r", "\n"], ' ', (string) $message);
        $contextStr = $context !== [] ? str_replace(["\r\n", "\r", "\n"], ' ', (string) json_encode($context)) : '';
        $entry = sprintf('[%s] [%s] %s', $date, $level, $sanitisedMessage) . ($contextStr ? ' ' . $contextStr : '') . "\n";
        file_put_contents($this->getLogFileName(), $entry, FILE_APPEND);
    }

    public function notice(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function warning(mixed $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
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
}
