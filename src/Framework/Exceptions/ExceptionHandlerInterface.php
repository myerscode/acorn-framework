<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Exceptions;

use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * Register a handler for a specific exception type.
     *
     * The handler receives the exception and can perform any logic (retry, notify, clean up, etc).
     * If the handler returns `true`, the exception is considered handled and will not be rendered.
     * If it returns `false` or `null`, normal rendering continues.
     *
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(T): ?bool $handler
     */
    public function catch(string $exceptionClass, callable $handler): void;

    /**
     * Register a class of exceptions that should not be reported.
     *
     * @param class-string<Throwable> ...$exceptions
     */
    public function dontReport(string ...$exceptions): void;

    /**
     * Render the exception to the console output.
     */
    public function render(OutputInterface $output, Throwable $exception): void;

    /**
     * Register a custom renderer for a specific exception type.
     *
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(OutputInterface, T): void $renderer
     */
    public function renderUsing(string $exceptionClass, callable $renderer): void;
    /**
     * Report the exception (e.g. log it).
     */
    public function report(Throwable $exception): void;

    /**
     * Register a custom reporter for a specific exception type.
     *
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(T): void $reporter
     */
    public function reportUsing(string $exceptionClass, callable $reporter): void;

    /**
     * Determine if the exception should be reported.
     */
    public function shouldReport(Throwable $exception): bool;
}
