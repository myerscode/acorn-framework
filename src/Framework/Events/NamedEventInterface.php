<?php

namespace Myerscode\Acorn\Framework\Events;

interface NamedEventInterface
{
    /**
     * Gets the event name.
     *
     * @return string
     */
    public function eventName(): string;
}
