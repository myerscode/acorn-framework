<?php

namespace Myerscode\Acorn\Framework\Events;

use Myerscode\Acorn\Framework\Events\Exception\EventConfigException;

class NamedEvent extends Event
{

    /**
     * Custom name of the event
     *
     * @var string
     */
    protected string $eventName;

    public function __construct(string $name)
    {
        if (is_null($name) || empty($name) || strlen($name) <= 0) {
            throw new EventConfigException();
        }

        $this->eventName = $name;
    }

    public function eventName(): string
    {
        return $this->eventName;
    }
}
