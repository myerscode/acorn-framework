<?php

namespace Myerscode\Acorn\Framework\Events;

class CallableEventManager
{

    /**
     * Array of callable-listeners.
     *
     * @var CallableListener[]
     */
    protected static array $listeners = [];

    /**
     * Removes all registered callable listeners.
     */
    public static function clear(): void
    {
        static::$listeners = [];
    }

    public static function create($callable): CallableListener
    {
        $listener = new CallableListener($callable);

        self::$listeners[] = $listener;

        return $listener;
    }

    /**
     * Finds the listener from the collection by its callable.
     */
    public static function findByCallable(callable $callable): CallableListener|false
    {
        foreach (static::$listeners as $listener) {
            if ($listener->getCallable() === $callable) {
                return $listener;
            }
        }

        return false;
    }

    /**
     * Get collection of registered callable listeners
     *
     * @return CallableListener[]
     */
    public static function listeners(): array
    {
        return static::$listeners;
    }
}
