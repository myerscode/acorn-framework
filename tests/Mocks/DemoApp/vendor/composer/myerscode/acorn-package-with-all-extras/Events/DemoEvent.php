<?php

declare(strict_types=1);

namespace App\Events;

use Myerscode\Acorn\Framework\Events\Event;

class DemoEvent extends Event
{

    public function __construct(readonly int $timestamp)
    {
        //
    }

}
