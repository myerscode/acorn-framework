<?php

namespace App\Events;

use Myerscode\Acorn\Framework\Events\Event;

class DemoEvent extends Event
{

    public function __construct(readonly int $timestamp)
    {
        //
    }

}
