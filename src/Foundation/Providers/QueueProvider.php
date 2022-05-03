<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

use function Myerscode\Acorn\Foundation\config;

class QueueProvider extends ServiceProvider
{
    protected $provides = [
        'queue',
        QueueInterface::class,
        Dispatcher::class
    ];

    public function register(): void
    {
        $this->getContainer()->add(QueueInterface::class, config('app.queue'));

        $this->getContainer()->add('queue', fn() => $this->getContainer()->get(QueueInterface::class));

        $this->getContainer()->add(Dispatcher::class, fn(): Dispatcher => new Dispatcher($this->getContainer()->get(QueueInterface::class)));
    }
}
