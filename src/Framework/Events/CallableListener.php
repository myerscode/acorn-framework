<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Framework\Events\Exception\InvalidCallableConstructException;

class CallableListener implements ListenerInterface
{

    private $callable;

    public function __construct($callable)
    {
        if (!($callable instanceof Closure) && !is_callable($callable)) {
            throw new InvalidCallableConstructException();
        }

        $this->callable = $callable;
    }

    /**
     * @return mixed
     */
    public function getCallable()
    {
        return $this->callable;
    }

    /**
     * Handles an event.
     *
     * @param  Event  $event
     */
    public function handle(Event $event)
    {
        call_user_func($this->callable, $event);
    }
}
