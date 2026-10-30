<?php

namespace Myerscode\Acorn\Framework\Events;

/**
 * Static registry for callable listeners.
 *
 * @deprecated Use Dispatcher::callableListeners() and Dispatcher::findCallableListener() instead.
 *             This class is retained for backward compatibility but the canonical listener
 *             registry is now instance-scoped on the Dispatcher.
 */
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

    /**
     * @deprecated Use Dispatcher::createCallableListener() instead.
     */
    public static function create(mixed $callable): CallableListener
    {
        $callableListener = new CallableListener($callable);

        self::$listeners[] = $callableListener;

        return $callableListener;
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

    /**
     * Register a pre-created CallableListener (used by Dispatcher internally).
     */
    public static function register(CallableListener $listener): void
    {
        self::$listeners[] = $listener;
    }
}
