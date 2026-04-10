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
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Get a configuration value from the application
 *
 * @return array|mixed|null
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function config(?string $key = null, mixed $default = null): mixed
{
    /** @var Config $config */
    $config = Container::getInstance()->get('config');

    if ($key) {
        return $config->store()->get(trim($key), $default);
    }

    return $config->values();
}

/**
 * Get the available container instance.
 */
function container(?string $abstract = null): mixed
{
    if (is_null($abstract)) {
        return Container::getInstance();
    }

    return Container::getInstance()->get($abstract);
}

/**
 *
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function dispatch(EventInterface $event): void
{
    Container::getInstance()->get(Dispatcher::class)->dispatch($event);
}

function emit(string $eventName, mixed $params = null): void
{
    Container::getInstance()->get(Dispatcher::class)->emit($eventName, $params);
}

function file(string $path = ''): FileUtility
{
    return new FileUtility($path);
}

function text(string|TextUtility $text = ''): TextUtility
{
    return new TextUtility($text);
}

/**
 * @param array<mixed>|BagUtility $bag
 */
function bag(array|BagUtility $bag = []): BagUtility
{
    return new BagUtility($bag);
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function output(): DisplayOutput
{
    return Container::getInstance()->get('output');
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function input(): Input
{
    return Container::getInstance()->get('input');
}

/**
 * @throws ContainerExceptionInterface
 * @throws NotFoundExceptionInterface
 */
function terminal(): Terminal
{
    return Container::getInstance()->get(Terminal::class);
}
