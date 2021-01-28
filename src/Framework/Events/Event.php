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
        return get_class($this);
    }

    /**
     * Stop event propagation.
     *
     * @return $this
     */
    public function stopPropagation(): self
    {
        $this->propagationStopped = true;

        return $this;
    }

    /**
     * Checks whether propagation was stopped.
     *
     * @return bool
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
