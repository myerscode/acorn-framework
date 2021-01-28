<?php

namespace Myerscode\Acorn\Framework\Events;


abstract class Listener implements ListenerInterface
{
    /**
     * @var string[]|string
     */
    protected $listensFor;

    /**
     * @return string[]|string
     */
    public function listensFor()
    {
        if (is_null($this->listensFor)) {
            return [];
        }

        return $this->listensFor;
    }
}
