<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Output\OutputInterface;

interface ConsoleOutputInterface extends OutputInterface
{
    public function line(string $message);

    public function verbose(string $message);

    public function veryVerbose(string $message);

    public function debug(string $message);

    public function table(array $headers, array $rows);

    public function text($message);

    public function comment($message);

    public function success($message);

    public function error($message);

    public function warning($message);

    public function note($message);

    public function info($message);

    public function caution($message);
}
