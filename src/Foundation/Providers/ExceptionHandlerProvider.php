<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Foundation\Exceptions\ExceptionHandler;
use Myerscode\Acorn\Framework\Exceptions\ErrorHandler;
use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Override;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

use function Myerscode\Acorn\Foundation\config;

class ExceptionHandlerProvider extends ServiceProvider
{
    #[Override]
    protected $provides = [
        ExceptionHandlerInterface::class,
    ];

    public function register(): void
    {
        $this->getContainer()->addShared(ExceptionHandlerInterface::class, function () {
            $logger = $this->getContainer()->get('logger');
            $handler = new ExceptionHandler($logger);
            $handler->register();

            $this->bindDefaultHandler($handler);
            $this->bindConfiguredHandlers($handler);
            $this->bindConfiguredDontReport($handler);

            return $handler;
        });
    }

    private function bindConfiguredDontReport(ExceptionHandler $handler): void
    {
        /** @var array<class-string<Throwable>> $dontReport */
        $dontReport = config('error.dont_report', []);

        if ($dontReport !== []) {
            $handler->dontReport(...$dontReport);
        }
    }

    private function bindConfiguredHandlers(ExceptionHandler $handler): void
    {
        /** @var array<class-string<Throwable>, class-string<ErrorHandler>|array<class-string<ErrorHandler>>> $handlers */
        $handlers = config('error.handlers', []);

        foreach ($handlers as $exceptionClass => $errorHandlers) {
            $errorHandlers = is_array($errorHandlers) ? $errorHandlers : [$errorHandlers];

            foreach ($errorHandlers as $errorHandlerClass) {
                $handler->catch($exceptionClass, function (Throwable $exception) use ($errorHandlerClass): ?bool {
                    /** @var ErrorHandler $errorHandler */
                    $errorHandler = $this->getContainer()->get($errorHandlerClass);
                    $output = $this->getContainer()->get('output');

                    return $errorHandler->handle($exception, $output);
                });
            }
        }
    }

    private function bindDefaultHandler(ExceptionHandler $handler): void
    {
        /** @var class-string<ErrorHandler>|array<class-string<ErrorHandler>>|null $default */
        $default = config('error.default', null);

        if ($default === null) {
            return;
        }

        $defaultHandlers = is_array($default) ? $default : [$default];

        $handler->setDefaultRenderer(function (OutputInterface $output, Throwable $exception) use ($defaultHandlers): void {
            foreach ($defaultHandlers as $handlerClass) {
                /** @var ErrorHandler $errorHandler */
                $errorHandler = $this->getContainer()->get($handlerClass);
                $result = $errorHandler->handle($exception, $output);

                if ($result === true) {
                    return;
                }
            }
        });
    }
}
