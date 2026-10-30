<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

class Event implements EventInterface, NamedEventInterface
{
    /**
     * Whether the event propagation is stopped.
     */
    protected bool $propagationStopped = false;

    public function eventName(): string
    {
        return static::class;
    }

    /**
     * Checks whether propagation was stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stop event propagation.
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;

        return $this;
    }
}
