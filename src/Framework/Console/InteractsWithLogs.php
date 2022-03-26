<?php

namespace Myerscode\Acorn\Framework\Console;

use Psr\Log\LoggerInterface;

trait InteractsWithLogs
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface|null
     */
    protected $logger;

    /**
     * Sets a logger.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
