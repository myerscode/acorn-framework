<?php

namespace Myerscode\Acorn\Framework\Events;

abstract class Listener implements ListenerInterface
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = [];

    /**
     * Should the listener action be queued or handled synchronously
     *
     * @var boolean
     */
    protected bool $shouldQueue = false;

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
