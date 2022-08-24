<?php

namespace Myerscode\Acorn\Foundation\Console;

use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;

class VoidOutput extends NullOutput implements ConsoleOutputInterface
{

    public function text($message): void
    {
        // do nothing
    }

    public function comment($message): void
    {
        // do nothing
    }

    public function success($message): void
    {
        // do nothing
    }

    public function error($message): void
    {
        // do nothing
    }

    public function warning($message): void
    {
        // do nothing
    }

    public function note($message): void
    {
        // do nothing
    }

    public function info($message): void
    {
        // do nothing
    }

    public function caution($message): void
    {
        // do nothing
    }

    public function line(string $message): void
    {
        // do nothing
    }

    public function verbosity(): int
    {
        // do nothing
    }

    public function verbose(string $message): void
    {
        // do nothing
    }

    public function veryVerbose(string $message): void
    {
        // do nothing
    }

    public function debug(string $message): void
    {
        // do nothing
    }

    public function table(array $headers, array $rows): void
    {
        // do nothing
    }
}
