<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Output extends SymfonyStyle
{

    public function line(string $message): void
    {
        $this->writeln($message);
    }

    /**
     * Write a verbose message that is only output when the -v is present
     *
     * @param  string  $messages
     */
    public function verbose(string $messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Write a very verbose message that is only output when the -vv is present
     *
     * @param  string  $messages
     */
    public function veryVerbose(string $messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    /**
     * Write a very verbose message that is only output when the -vvv is present
     *
     * @param  string  $messages
     */
    public function debug(string $messages): void
    {
        $this->writeln($messages, OutputInterface::VERBOSITY_DEBUG);
    }

}
