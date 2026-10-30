<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class CommandError extends Listener
{
    public function __construct(private readonly DisplayOutput $displayOutput)
    {
        //
    }

    #[Override]
    public function handle(EventInterface $event): void
    {
        if (!$event instanceof CommandErrorEvent) {
            return;
        }

        $command = $event->consoleErrorEvent->getCommand();
        $message = sprintf('Error running command <info>%s</info>', $command?->getName() ?? 'UNKNOWN');
        $this->displayOutput->verbose($message);
    }

    public function listensFor(): array|string
    {
        return CommandErrorEvent::class;
    }
}
