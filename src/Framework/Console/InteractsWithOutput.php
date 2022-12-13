<?php

namespace Myerscode\Acorn\Framework\Console;

use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;

trait InteractsWithOutput
{
    protected DisplayOutputInterface $output;

    /**
     * Write a very verbose message that is only output when the -vvv is present
     */
    public function debug(string|array $message): void
    {
        $this->output->debug($message);
    }

    public function error(string|array $message): void
    {
        $this->output->error($message);
    }

    public function info(string|array $message): void
    {
        $this->output->info($message);
    }

    /**
     * Returns whether verbosity is debug (-vvv).
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }

    /**
     * Returns whether verbosity is quiet (-q).
     */
    public function isQuiet(): bool
    {
        return $this->output->isQuiet();
    }

    /**
     * Returns whether verbosity is verbose (-v).
     */
    public function isVerbose(): bool
    {
        return $this->output->isVerbose();
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     */
    public function isVeryVerbose(): bool
    {
        return $this->output->isVeryVerbose();
    }

    public function line(string|array $message): void
    {
        $this->output->line($message);
    }

    public function success(string|array $message): void
    {
        $this->output->success($message);
    }

    public function verbose(string|array $message): void
    {
        $this->output->verbose($message);
    }

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int
    {
        return $this->output->getVerbosity();
    }

    public function veryVerbose(string|array $message): void
    {
        $this->output->veryVerbose($message);
    }
}
