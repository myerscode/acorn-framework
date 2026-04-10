<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Events\Listener;

class CommandError extends Listener
{
    /**
     * @var string[]|string
     */
    #[\Override]
    protected string|array $listensFor = CommandErrorEvent::class;

    public function __construct(private readonly DisplayOutput $displayOutput)
    {
        //
    }

    public function __invoke(CommandErrorEvent $commandErrorEvent): void
    {
        if ($command = $commandErrorEvent->consoleErrorEvent->getCommand() instanceof Command) {
            $commaName = $commandErrorEvent->consoleErrorEvent->getCommand()->getName();
        }

        $message = sprintf('Error running command <info>%s</info>', $commaName ?? 'UNKNOWN');

        $this->displayOutput->verbose($message);
    }
}
