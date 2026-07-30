<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class BeforeCommandRun extends Listener
{
    public function __construct(private readonly DisplayOutput $displayOutput)
    {
        //
    }

    #[Override]
    public function handle(EventInterface $event): void
    {
        if (!$event instanceof CommandBeforeEvent) {
            return;
        }

        $command = $event->consoleCommandEvent->getCommand();
        $message = sprintf('Before running command <info>%s</info>', $command?->getName() ?? 'UNKNOWN');
        $this->displayOutput->verbose($message);
    }

    public function listensFor(): array|string
    {
        return CommandBeforeEvent::class;
    }
}
