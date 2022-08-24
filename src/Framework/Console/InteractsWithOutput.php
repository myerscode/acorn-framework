<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Output\OutputInterface;

trait InteractsWithOutput
{
    protected ConsoleOutputInterface $output;

    protected array $verbosityMap = [
            'v' => OutputInterface::VERBOSITY_VERBOSE,
            'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            'vvv' => OutputInterface::VERBOSITY_DEBUG,
            'quiet' => OutputInterface::VERBOSITY_QUIET,
            'normal' => OutputInterface::VERBOSITY_NORMAL,
        ];

    /**
     * Write a very verbose message that is only output when the -vvv is present
     */
    public function debug(string $message): void
    {
        $this->output->debug($message);
    }

    public function error(string $string): void
    {
        $this->output->error($string);
    }

    public function info(string $string, int|string $verbosity = null): void
    {
        $this->line($string, 'info', $verbosity);
    }

    public function line(string $string, string $style = null, int|string $verbosity = null): void
    {
        $styled = $style ? sprintf('<%s>%s</%s>', $style, $string, $style) : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     */
    protected function parseVerbosity(string|int|null $level = null): int
    {
        if (isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (!is_int($level)) {
            $level = $this->verbosityMap['normal'];
        }

        return $level;
    }

    public function verbose(string $message): void
    {
        $this->output->verbose($message);
    }

    public function veryVerbose(string $message): void
    {
        $this->output->veryVerbose($message);
    }

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int
    {
        return $this->output->getVerbosity();
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

    /**
     * Returns whether verbosity is debug (-vvv).
     */
    public function isDebug(): bool
    {
        return $this->output->isDebug();
    }
}
