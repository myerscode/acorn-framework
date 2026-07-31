<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Exceptions;

use Throwable;

/**
 * Base class for exception handlers that can be mapped via config.
 *
 * Extend this class and implement handle() to define what happens when a
 * specific exception type is caught. Return true to suppress rendering,
 * or false/null to let normal error rendering continue.
 */
abstract class ErrorHandler
{
    /**
     * Handle the exception.
     *
     * Return `true` to mark as handled (suppresses rendering).
     * Return `false` or `null` to allow normal rendering to continue.
     */
    abstract public function handle(Throwable $exception): ?bool;
}
