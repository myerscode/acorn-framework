<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console\Display;

use Symfony\Component\Console\Output\OutputInterface;

interface DisplayOutputInterface extends OutputInterface
{
    /** @param string|string[] $message */
    public function caution(string|array $message): void;

    /** @param string|string[] $message */
    public function comment(string|array $message): void;

    /**
     * Write a very verbose message that is only output when the -vvv is present
     *
     * @param string|string[] $message
     */
    public function debug(string|array $message): void;

    /** @param string|string[] $message */
    public function error(string|array $message): void;

    /** @param string|string[] $message */
    public function info(string|array $message): void;

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
     *
     * @param string|string[] $message
     */
    public function line(string|array $message): void;

    /** @param string|string[] $message */
    public function note(string|array $message): void;

    /** @param string|string[] $message */
    public function success(string|array $message): void;

    /**
     * @param array<mixed> $headers
     * @param array<mixed> $rows
     */
    public function table(array $headers, array $rows): void;

    /** @param string|string[] $message */
    public function text(string|array $message): void;

    /**
     * Write a verbose message that is only output when the -v is present
     *
     * @param string|string[] $message
     */
    public function verbose(string|array $message): void;

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int;

    /**
     * Write a very verbose message that is only output when the -vv is present
     *
     * @param string|string[] $message
     */
    public function veryVerbose(string|array $message): void;

    /** @param string|string[] $message */
    public function warning(string|array $message): void;
}
