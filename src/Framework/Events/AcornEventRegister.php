<?php

namespace Myerscode\Acorn\Framework\Events;

class AcornEventRegister
{

    protected array $events = [];

    public function getEvents(): array
    {
        return $this->events;
    }
}
