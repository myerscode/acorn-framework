<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Config;

/**
 * Error handling configuration.
 *
 * Map exception class names to ErrorHandler classes.
 * Each key is a fully-qualified exception class name, and the value
 * is either a single ErrorHandler class or an array of them.
 *
 * Handlers are resolved from the container, so they can have
 * dependencies injected via constructor.
 *
 * Example:
 *
 *   return [
 *       'handlers' => [
 *           \App\Exceptions\PaymentFailedException::class => \App\Handlers\NotifyOpsHandler::class,
 *           \App\Exceptions\TimeoutException::class => [
 *               \App\Handlers\RetryHandler::class,
 *               \App\Handlers\LogTimeoutHandler::class,
 *           ],
 *       ],
 *       'dont_report' => [
 *           \App\Exceptions\ValidationException::class,
 *       ],
 *   ];
 */
return [
    'handlers' => [
        // ExceptionClass::class => HandlerClass::class,
        // ExceptionClass::class => [HandlerClass::class, AnotherHandlerClass::class],
    ],
    'dont_report' => [
        // ExceptionClass::class,
    ],
];
