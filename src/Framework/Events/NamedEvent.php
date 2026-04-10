<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

use Override;
use Myerscode\Acorn\Framework\Events\Exception\EventConfigException;

class NamedEvent extends Event
{
    /**
     * Custom name of the event
     */
    protected string $eventName;

    public function __construct(string $name)
    {
        if (is_null($name) || ($name === '' || $name === '0') || strlen($name) <= 0) {
            throw new EventConfigException();
        }

        $this->eventName = $name;
    }

    #[Override]
    public function eventName(): string
    {
        return $this->eventName;
    }
}
