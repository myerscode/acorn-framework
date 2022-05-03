<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Framework\Log\NullLogger;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;

class LogProvider extends ServiceProvider
{
    protected $provides = [
        NullLogger::class,
        'logger',
    ];

    public function register(): void
    {
        $this->getContainer()->add(NullLogger::class);
        $this->getContainer()->add('logger', fn() => $this->getContainer()->get(NullLogger::class));
    }
}
