<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\Event;
use Symfony\Component\Console\Event\ConsoleErrorEvent;

class CommandErrorEvent extends Event
{
    protected string $eventName = 'acorn.command.error';

    public function __construct(public ConsoleErrorEvent $consoleErrorEvent)
    {
    }
}
