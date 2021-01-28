<?php

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Framework\Events\Dispatcher;

if (!function_exists('container')) {
    /**
     * Get the available container instance.
     *
     * @param  string|null  $abstract
     *
     * @return array|mixed|Container|object
     */
    function container(string $abstract = null)
    {
        if (is_null($abstract)) {
            return Container::getInstance();
        }

        return Container::getInstance()->get($abstract);
    }
}

if (!function_exists('emmit')) {
    function emmit($eventName, $params = null)
    {
        return Container::getInstance()->get(Dispatcher::class)->emit($eventName, $params );
    }
}

if (!function_exists('dispatch')) {
    function dispatch($event)
    {
        return Container::getInstance()->get(Dispatcher::class)->dispatch($event);
    }
}
