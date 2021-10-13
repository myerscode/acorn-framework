<?php

namespace Myerscode\Acorn\Framework\Events;

use Closure;

class CallableEventManager
{


    /**
     * Array of callable-listeners.
     *
     * @var CallableListener[]
     */
    protected static array $listeners = [];


    public static function create($callable): CallableListener
    {
        $listener = new CallableListener($callable);

        self::$listeners[] = $listener;

        return $listener;
    }

    /**
     * Removes all registered callable-listeners.
     */
    public static function clear()
    {
        static::$listeners = [];
    }

    /**
     * Finds the listener from the collection by its callable.
     *
     * @param  callable  $callable
     */
    public static function findByCallable($callable): CallableListener|false
    {
        foreach (static::$listeners as $listener) {
            if ($listener->getCallable() == $callable) {
                return $listener;
            }
        }

        return false;
    }
}
