<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;
use Myerscode\Acorn\Framework\Events\Exception\InvalidCallableConstructException;

class CallableListener implements ListenerInterface
{

    public function __construct(protected $callable)
    {
        if ($callable instanceof Closure) {
            return;
        }
        if (is_callable($callable)) {
            return;
        }
        throw new InvalidCallableConstructException();
    }

    /**
     * @return mixed
     */
    public function getCallable(): Callable
    {
        return $this->callable;
    }

    /**
     * Handles an event.
     */
    public function handle(EventInterface $event): void
    {
        call_user_func($this->callable, $event);
    }

    public function shouldQueue(): bool
    {
        $shouldQueue = false;

        if (method_exists($this->callable, 'shouldQueue')) {
            $shouldQueue = $this->callable->shouldQueue();
        } elseif (property_exists($this->callable, 'shouldQueue')) {
            $shouldQueue = $this->callable->shouldQueue;
        }

        return filter_var($shouldQueue, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}
