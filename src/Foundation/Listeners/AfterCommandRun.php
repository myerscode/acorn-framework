<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class AfterCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    #[Override]
    protected string|array $listensFor = CommandAfterEvent::class;

    public function __construct(private readonly DisplayOutput $displayOutput)
    {
        //
    }

    #[Override]
    public function handle(EventInterface $event): void
    {
        if (!$event instanceof CommandAfterEvent) {
            return;
        }

        $command = $event->consoleTerminateEvent->getCommand();
        $message = sprintf('After running command <info>%s</info>', $command?->getName() ?? 'UNKNOWN');
        $this->displayOutput->verbose($message);
    }
}
