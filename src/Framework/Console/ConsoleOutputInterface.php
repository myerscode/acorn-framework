<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Output\OutputInterface;

interface ConsoleOutputInterface extends OutputInterface
{
    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param  string  $message
     */
    public function line(string $message);

    /**
     * Write a verbose message that is only output when the -v is present
     *
     * @param  string  $message
     */
    public function verbose(string $message);

    /**
     * Write a very verbose message that is only output when the -vv is present
     *
     * @param  string  $message
     */
    public function veryVerbose(string $message);

    /**
     * Write a very verbose message that is only output when the -vvv is present
     *
     * @param  string  $message
     */
    public function debug(string $message);

    public function table(array $headers, array $rows);

    public function text(string|array $message);

    public function comment(string|array $message);

    public function success(string|array $message);

    public function error(string|array $message);

    public function warning(string|array $message);

    public function note(string|array $message);

    public function info(string|array $message);

    public function caution(string|array $message);
}
