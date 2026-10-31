<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Exceptions;

use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class ExceptionHandler implements ExceptionHandlerInterface
{
    /** @var array<class-string<Throwable>, list<callable>> */
    private array $catchHandlers = [];

    /** @var array<class-string<Throwable>, callable> */
    private array $customRenderers = [];

    /** @var array<class-string<Throwable>, callable> */
    private array $customReporters = [];

    /** @var callable(OutputInterface, Throwable): void|null */
    private mixed $defaultRenderer = null;

    /** @var array<class-string<Throwable>> */
    private array $dontReportExceptions = [];

    public function __construct(private readonly LoggerInterface $logger)
    {
        //
    }

    /**
     * Register a handler for a specific exception type.
     *
     * The handler receives the exception and can perform any logic (retry, notify, clean up, etc).
     * If the handler returns `true`, the exception is considered handled and will not be rendered.
     * If it returns `false` or `null`, normal rendering continues.
     *
     * Multiple handlers can be registered for the same exception type — they run in order
     * until one returns `true`.
     *
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(T): ?bool $handler
     */
    public function catch(string $exceptionClass, callable $handler): void
    {
        $this->catchHandlers[$exceptionClass][] = $handler;
    }

    /**
     * @param class-string<Throwable> ...$exceptions
     */
    public function dontReport(string ...$exceptions): void
    {
        foreach ($exceptions as $exception) {
            $this->dontReportExceptions[] = $exception;
        }
    }

    /**
     * Render the exception to console output.
     *
     * Before rendering, registered catch handlers are invoked. If any handler
     * returns `true`, the exception is considered handled and rendering is skipped.
     */
    public function render(OutputInterface $output, Throwable $exception): void
    {
        // Run catch handlers first — if one handles it, skip rendering
        if ($this->runCatchHandlers($exception)) {
            return;
        }

        // Check for custom renderer
        foreach ($this->customRenderers as $exceptionClass => $renderer) {
            if ($exception instanceof $exceptionClass) {
                $renderer($output, $exception);

                return;
            }
        }

        // Use configured default renderer or built-in fallback
        if ($this->defaultRenderer !== null) {
            ($this->defaultRenderer)($output, $exception);

            return;
        }

        $this->renderFallback($output, $exception);
    }

    /**
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(OutputInterface, T): void $renderer
     */
    public function renderUsing(string $exceptionClass, callable $renderer): void
    {
        $this->customRenderers[$exceptionClass] = $renderer;
    }

    /**
     * Report the exception by logging it.
     */
    public function report(Throwable $exception): void
    {
        if (!$this->shouldReport($exception)) {
            return;
        }

        // Check for custom reporter
        foreach ($this->customReporters as $exceptionClass => $reporter) {
            if ($exception instanceof $exceptionClass) {
                $reporter($exception);

                return;
            }
        }

        $this->logger->error($exception->getMessage(), [
            'exception' => $exception,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }

    /**
     * @template T of Throwable
     * @param class-string<T> $exceptionClass
     * @param callable(T): void $reporter
     */
    public function reportUsing(string $exceptionClass, callable $reporter): void
    {
        $this->customReporters[$exceptionClass] = $reporter;
    }

    /**
     * Set the default renderer used when no specific handler or renderer matches.
     *
     * @param callable(OutputInterface, Throwable): void $renderer
     */
    public function setDefaultRenderer(callable $renderer): void
    {
        $this->defaultRenderer = $renderer;
    }

    /**
     * Determine if the exception should be reported.
     */
    public function shouldReport(Throwable $exception): bool
    {
        foreach ($this->dontReportExceptions as $exceptionClass) {
            if ($exception instanceof $exceptionClass) {
                return false;
            }
        }

        return true;
    }

    /**
     * Built-in fallback renderer when no default handler is configured.
     */
    private function renderFallback(OutputInterface $output, Throwable $exception): void
    {
        $stderr = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;

        $stderr->writeln('');
        $stderr->writeln(sprintf('  <error> %s </error>', $exception::class));
        $stderr->writeln('');
        $stderr->writeln(sprintf('  <fg=red>%s</>', $exception->getMessage()));

        if ($output->isVerbose()) {
            $stderr->writeln('');
            $stderr->writeln(sprintf('  <comment>%s:%d</comment>', $exception->getFile(), $exception->getLine()));
        }

        if ($output->isVeryVerbose()) {
            $stderr->writeln('');
            $stderr->writeln('  <comment>Exception trace:</comment>');

            foreach ($exception->getTrace() as $i => $frame) {
                $file = $frame['file'] ?? 'unknown';
                $line = $frame['line'] ?? 0;
                $class = $frame['class'] ?? '';
                $function = $frame['function'] ?? '';
                $type = $frame['type'] ?? '';

                $call = $class !== '' ? $class . $type . $function . '()' : $function . '()';
                $stderr->writeln(sprintf('  <fg=gray>%d. %s</>', $i + 1, $call));
                $stderr->writeln(sprintf('     <fg=gray>%s:%d</>', $file, $line));
            }
        }

        $stderr->writeln('');
    }

    /**
     * Run registered catch handlers for the exception.
     *
     * Returns `true` if the exception was handled (a handler returned true).
     */
    private function runCatchHandlers(Throwable $exception): bool
    {
        foreach ($this->catchHandlers as $exceptionClass => $handlers) {
            if ($exception instanceof $exceptionClass) {
                foreach ($handlers as $handler) {
                    $result = $handler($exception);

                    if ($result === true) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
