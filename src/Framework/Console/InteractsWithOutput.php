<?php

namespace Myerscode\Acorn\Framework\Console;

use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

trait InteractsWithOutput
{
    protected DisplayOutputInterface|OutputInterface $output;

    /**
     * Write a very verbose message that is only output when the -vvv is present
     *
     * @param string|string[] $message
     */
    public function debug(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->debug($message);

            return;
        }

        $this->output->writeln($this->normalise($message), OutputInterface::VERBOSITY_DEBUG);
    }

    /** @param string|string[] $message */
    public function error(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->error($message);

            return;
        }

        $this->output->writeln($this->normalise($message));
    }

    /** @param string|string[] $message */
    public function info(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->info($message);

            return;
        }

        $this->output->writeln($this->normalise($message));
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

    /** @param string|string[] $message */
    public function line(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->line($message);

            return;
        }

        $this->output->writeln($this->normalise($message));
    }

    /** @param string|string[] $message */
    public function success(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->success($message);

            return;
        }

        $this->output->writeln($this->normalise($message));
    }

    /** @param string|string[] $message */
    public function verbose(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->verbose($message);

            return;
        }

        $this->output->writeln($this->normalise($message), OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int
    {
        return $this->output->getVerbosity();
    }

    /** @param string|string[] $message */
    public function veryVerbose(string|array $message): void
    {
        if ($this->output instanceof DisplayOutputInterface) {
            $this->output->veryVerbose($message);

            return;
        }

        $this->output->writeln($this->normalise($message), OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    /**
     * Normalise message to string for plain OutputInterface.
     *
     * @param string|string[] $message
     * @return string|string[]
     */
    private function normalise(string|array $message): string|array
    {
        return $message;
    }
}
