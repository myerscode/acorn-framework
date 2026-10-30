<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

interface NamedEventInterface
{
    /**
     * Gets the event name.
     */
    public function eventName(): string;
}
