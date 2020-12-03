<?php

namespace Myerscode\Acorn\Framework\Events;

use Myerscode\Acorn\Framework\Events\Exception\EventConfigException;

class AcornEvent
{
    protected string $eventName;

    public function __construct()
    {
        if (is_null($this->eventName) || strlen($this->eventName) <= 0) {
            throw new EventConfigException();
        }
    }

    public function eventName(): string
    {
        return $this->eventName;
    }

    public function __toString(): string
    {
        return $this->eventName();
    }
}
