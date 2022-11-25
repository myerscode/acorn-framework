<?php

namespace Myerscode\Acorn\Framework\Console\Display;

use Symfony\Component\Console\Output\OutputInterface;

interface DisplayOutputInterface extends OutputInterface
{
    public function caution(string|array $message);

    public function comment(string|array $message);

    /**
     * Write a very verbose message that is only output when the -vvv is present
     */
    public function debug(string|array $message);

    public function error(string|array $message);

    public function info(string|array $message);

    /**
     * Returns whether verbosity is debug (-vvv).
     */
    public function isDebug(): bool;

    /**
     * Returns whether verbosity is quiet (-q).
     */
    public function isQuiet(): bool;

    /**
     * Returns whether verbosity is verbose (-v).
     */
    public function isVerbose(): bool;

    /**
     * Returns whether verbosity is very verbose (-vv).
     */
    public function isVeryVerbose(): bool;

    /**
     * Writes a message to the output and adds a newline at the end.
     */
    public function line(string|array $message);

    public function note(string|array $message);

    public function success(string|array $message);

    /**
     * @param  array  $headers
     * @param  array  $rows
     */
    public function table(array $headers, array $rows);

    public function text(string|array $message);

    /**
     * Write a verbose message that is only output when the -v is present
     */
    public function verbose(string|array $message);

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int;

    /**
     * Write a very verbose message that is only output when the -vv is present
     */
    public function veryVerbose(string|array $message);

    public function warning(string|array $message);
}
