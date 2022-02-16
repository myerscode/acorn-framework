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

    public function error(string $string, int|string $verbosity = null)
    {
        $this->output->error($string);
    }

    public function info(string $string, int|string $verbosity = null)
    {
        $this->line($string, 'info', $verbosity);
    }

    public function line(string $string, string $style = null, int|string $verbosity = null)
    {
        $styled = $style ? "<$style>$string</$style>" : $string;

        $this->output->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     *
     * @param  string|int|null  $level
     *
     * @return int
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

}
