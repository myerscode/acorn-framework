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
     * Handle the event.
     *
     * Subclasses implement their logic by overriding handle(), or by defining
     * an __invoke() method (the dispatcher calls handle(), which bridges to
     * __invoke() when present). Overriding handle() takes precedence.
     */
    public function handle(EventInterface $event): void
    {
        if (is_callable($this)) {
            $this($event);
        }
    }

    /**
     * Should this listener queue its response
     */
    public function shouldQueue(): bool
    {
        return $this->shouldQueue;
    }
}
