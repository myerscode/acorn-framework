<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

abstract class Listener implements ListenerInterface
{
    /**
     * Should the listener action be queued or handled synchronously
     */
    protected bool $shouldQueue = false;

    /**
     * Events this listener should be registered for.
     * Subclasses MUST implement this method to declare which events they handle.
     *
     * @return string[]|string
     */
    abstract public function listensFor(): array|string;

    /**
     * Handle the event - subclasses should override this method.
     */
    public function handle(EventInterface $event): void
    {
        // Default no-op; subclasses override this
    }

    /**
     * Should this listener queue its response
     */
    public function shouldQueue(): bool
    {
        return $this->shouldQueue;
    }
}
