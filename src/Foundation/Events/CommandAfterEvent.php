<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\Event;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class CommandAfterEvent extends Event
{
    protected string $eventName = 'acorn.command.after';

    public ConsoleTerminateEvent $commandEvent;

    public function __construct(ConsoleTerminateEvent $commandEvent)
    {
        $this->commandEvent = $commandEvent;
    }
}
