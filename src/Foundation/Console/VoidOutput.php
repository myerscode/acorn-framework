<?php

namespace Myerscode\Acorn\Foundation\Console;

use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;

class VoidOutput extends NullOutput implements ConsoleOutputInterface
{

    public function text($message)
    {
        // do nothing
    }

    public function comment($message)
    {
        // do nothing
    }

    public function success($message)
    {
        // do nothing
    }

    public function error($message)
    {
        // do nothing
    }

    public function warning($message)
    {
        // do nothing
    }

    public function note($message)
    {
        // do nothing
    }

    public function info($message)
    {
        // do nothing
    }

    public function caution($message)
    {
        // do nothing
    }

    public function line(string $message)
    {
        // do nothing
    }

    public function verbose(string $message)
    {
        // do nothing
    }

    public function veryVerbose(string $message)
    {
        // do nothing
    }

    public function debug(string $message)
    {
        // do nothing
    }

    public function table(array $headers, array $rows)
    {
        // do nothing
    }
}
