<?php

namespace Myerscode\Acorn\Framework\Events;

interface NamedEventInterface
{
    /**
     * Gets the event name.
     */
    public function eventName(): string;
}
