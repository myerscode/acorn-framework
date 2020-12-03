<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\AcornEvent;

class CommandErrorEvent extends AcornEvent
{
    protected string $eventName = 'acorn.command.error';
}
