<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\AcornEvent;

class CommandAfterEvent extends AcornEvent
{
    protected string $eventName = 'acorn.command.after';
}
