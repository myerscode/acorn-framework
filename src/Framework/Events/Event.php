<?php

namespace Myerscode\Acorn\Framework\Events;

class Event implements EventInterface, NamedEventInterface
{
    /**
     * Whether the event propagation is stopped.
     *
     * @var boolean
     */
    protected bool $propagationStopped = false;

    public function eventName(): string
    {
        return $this::class;
    }

    /**
     * Stop event propagation.
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;

        return $this;
    }

    /**
     * Checks whether propagation was stopped.
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
