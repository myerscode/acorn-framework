<?php

namespace Myerscode\Acorn\Framework\Exception;

use League\BooBoo\BooBoo;
use League\BooBoo\Formatter\CommandLineFormatter;
use League\BooBoo\Handler\LogHandler;
use Psr\Log\NullLogger;

class Handler
{
    private BooBoo $handler;

    function __construct()
    {
        $this->handler = new BooBoo($formatters = [], $handler = []);
        $this->handler->pushFormatter(new CommandLineFormatter);
        $this->handler->pushHandler(new LogHandler(new NullLogger()));
        $this->handler->register();
    }
}
