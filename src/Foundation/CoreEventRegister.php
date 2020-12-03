<?php

namespace Myerscode\Acorn\Foundation;

use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Events\AcornEventRegister;

class CoreEventRegister extends AcornEventRegister
{

    /**
     * The event listener mappings for the application.
     */
    protected array $events = [
            CommandBeforeEvent::class => [
                //
            ],
            CommandAfterEvent::class => [
                //
            ],
            CommandErrorEvent::class => [
                //
            ],
        ];
}
