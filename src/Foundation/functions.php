<?php

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Config\Config;

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

if (!function_exists('config')) {
    /**
     * @return mixed|Config
     */
    function config(string $key = null, $default = null)
    {
        /**
         * @var $pathCollection \Myerscode\Config\Config
         */
        $config = Container::getInstance()->manager()->get('config');

        if ($key) {
            return  $config->store()->get($key, $default);
        }

        return $config->values();
    }
}

if (!function_exists('emit')) {
    function emit($eventName, $params = null)
    {
        return Container::getInstance()->get(Dispatcher::class)->emit($eventName, $params);
    }
}

if (!function_exists('dispatch')) {
    function dispatch($event)
    {
        return Container::getInstance()->get(Dispatcher::class)->dispatch($event);
    }
}

if (!function_exists('output')) {
    /**
     * @return Myerscode\Acorn\Framework\Console\Output
     */
    function output()
    {
        return Container::getInstance()->manager()->get('output');
    }
}
