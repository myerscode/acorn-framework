<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Foundation\Exceptions\ExceptionHandler;
use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Override;

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

            return $handler;
        });
    }
}
