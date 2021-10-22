<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Output extends SymfonyStyle implements ConsoleOutputInterface
{

    public function line(string $message): void
    {
        $this->writeln($message);
    }

    /**
     * Write a verbose message that is only output when the -v is present
     */
    public function verbose(string $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Write a very verbose message that is only output when the -vv is present
     */
    public function veryVerbose(string $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    /**
     * Write a very verbose message that is only output when the -vvv is present
     */
    public function debug(string $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_DEBUG);
    }

}
