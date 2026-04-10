<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

abstract class Listener implements ListenerInterface
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = [];

    /**
     * Should the listener action be queued or handled synchronously
     */
    protected bool $shouldQueue = false;

    /**
     * Handle the event - subclasses should override this method.
     */
    public function handle(EventInterface $event): void
    {
        // Default no-op; subclasses override this
    }

    /**
     * @return string[]|string
     */
    public function listensFor(): array|string
    {
        return $this->listensFor;
    }

    /**
     * Should this listener queue its response
     */
    public function shouldQueue(): bool
    {
        return $this->shouldQueue;
    }
}
