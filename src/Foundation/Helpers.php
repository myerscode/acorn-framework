<?php

namespace Myerscode\Acorn\Foundation;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Console\Input\Input;
use Myerscode\Acorn\Framework\Container\Container;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Terminal\Terminal;
use Myerscode\Config\Config;
use Myerscode\Utilities\Bags\DotUtility as BagUtility;
use Myerscode\Utilities\Files\Utility as FileUtility;
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
    $config = Container::getInstance()->get('config');

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

function file(string|FileUtility $path = ''): FileUtility
{
    return new FileUtility($path);
}

function text(string|TextUtility $text = ''): TextUtility
{
    return new TextUtility($text);
}

function bag(array|BagUtility $bag = []): BagUtility
{
    return new BagUtility($bag);
}

function output(): DisplayOutput
{
    return Container::getInstance()->get('output');
}

function input(): Input
{
    return Container::getInstance()->get('input');
}

function terminal(): Terminal
{
    return Container::getInstance()->get(Terminal::class);
}
