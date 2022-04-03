<?php

namespace Myerscode\Acorn\Foundation;

use Myerscode\Acorn\Container;
use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Config\Config;
use Myerscode\Utilities\Strings\Utility as TextUtility;

/**
 * Get a configuration value from the application
 *
 * @param  string|null  $key
 * @param  null  $default
 *
 * @return array|mixed|null
 */
function config(string $key = null, $default = null)
{
    /**
     * @var $config Config
     */
    $config = Container::getInstance()->manager()->get('config');

    if ($key) {
        return $config->store()->get($key, $default);
    }

    return $config->values();
}

/**
 * Get the available container instance.
 *
 * @param  string|null  $abstract
 */
function container(string $abstract = null): mixed
{
    if (is_null($abstract)) {
        return Container::getInstance();
    }

    return Container::getInstance()->get($abstract);
}

/**
 * @param $event
 *
 * @return mixed
 */
function dispatch(EventInterface $event): void
{
    Container::getInstance()->get(Dispatcher::class)->dispatch($event);
}

function emit($eventName, $params = null): void
{
    Container::getInstance()->get(Dispatcher::class)->emit($eventName, $params);
}

function text(string|TextUtility $text = ''): TextUtility
{
    return new TextUtility($text);
}

function output(): Output
{
    return Container::getInstance()->manager()->get('output');
}

function input(): Output
{
    return Container::getInstance()->manager()->get('input');
}
