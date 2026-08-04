<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Config;

use Myerscode\Acorn\Foundation\Exceptions\Handlers\DefaultHandler;
use Myerscode\Acorn\Foundation\Exceptions\Handlers\PanicHandler;
use Myerscode\Acorn\Framework\Exceptions\AppPanicException;

/**
 * Error handling configuration.
 *
 * 'handlers' — map exception class names to ErrorHandler classes.
 * Each key is a fully-qualified exception class name, and the value
 * is either a single ErrorHandler class or an array of them.
 *
 * 'default' — the handler (or handlers) that run for any exception
 * that doesn't match a specific entry in 'handlers'.
 *
 * 'dont_report' — exceptions that should not be logged.
 *
 * Handlers are resolved from the container, so they can have
 * dependencies injected via constructor.
 */
return [
    'default' => DefaultHandler::class,
    'handlers' => [
        AppPanicException::class => PanicHandler::class,
    ],
    'dont_report' => [
        // ExceptionClass::class,
    ],
];
