<?php

namespace Myerscode\Acorn\Framework\Exception;

use Myerscode\Acorn\Framework\Log\NullLogger;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run as ErrorHandler;

class Handler
{
    protected ErrorHandler $handler;

    function __construct(LoggerInterface $logger = null)
    {
        $this->handler = new ErrorHandler();
        $errorLogger = $logger ?? new NullLogger();
        $this->handler->pushHandler(new PlainTextHandler($errorLogger));
        $this->handler->register();
    }

    public function errorHandler(): ErrorHandler
    {
        return $this->handler;
    }
}
