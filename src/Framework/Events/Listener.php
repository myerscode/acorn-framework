<?php

namespace Myerscode\Acorn\Framework\Events;

abstract class Listener implements ListenerInterface
{
    /**
     * @var string[]|string
     */
    protected $listensFor;

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
        if (!isset($this->listensFor)) {
            return [];
        }

        return $this->listensFor;
    }

    /**
     * Should this listener queue its response
     *
     * @return bool
     */
    public function shouldQueue(): bool
    {
        return $this->shouldQueue;
    }
}
