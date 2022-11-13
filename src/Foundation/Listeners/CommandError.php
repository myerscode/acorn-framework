<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Events\Listener;

class CommandError extends Listener
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = CommandErrorEvent::class;

    public function __construct(private readonly Output $output)
    {
        //
    }

    public function __invoke(CommandErrorEvent $event): void
    {
        if ($command = $event->consoleErrorEvent->getCommand() instanceof Command) {
            $commaName = $event->consoleErrorEvent->getCommand()->getName();
        }

        $message = sprintf('Error running command <info>%s</info>', $commaName ?? 'UNKNOWN');

        $this->output->verbose($message);
    }
}
