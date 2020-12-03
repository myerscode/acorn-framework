<?php

namespace Myerscode\Acorn\Foundation\Events;

use Myerscode\Acorn\Framework\Events\AcornEvent;

class CommandBeforeEvent extends AcornEvent
{
    protected string $eventName = 'acorn.command.before';
}
