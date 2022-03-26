<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\Event;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandBeforeEvent extends Event
{
    protected string $eventName = 'acorn.command.before';

    public function __construct(public ConsoleCommandEvent $consoleCommandEvent)
    {
    }
}
