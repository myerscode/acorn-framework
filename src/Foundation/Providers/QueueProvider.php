<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Log\NullLogger;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Myerscode\Acorn\Framework\Queue\QueueInterface;
use Override;
use Psr\Log\LoggerInterface;

use function Myerscode\Acorn\Foundation\config;

class QueueProvider extends ServiceProvider
{
    #[Override]
    protected $provides = [
        'queue',
        QueueInterface::class,
        Dispatcher::class,
    ];

    public function register(): void
    {
        $this->getContainer()->add(QueueInterface::class, config('app.queue'));

        $this->getContainer()->add('queue', fn () => $this->getContainer()->get(QueueInterface::class));

        $this->getContainer()->addShared(Dispatcher::class, function (): Dispatcher {
            $container = $this->getContainer();
            $logger = $container->has('logger') ? $container->get('logger') : new NullLogger();

            return new Dispatcher(
                $container->get(QueueInterface::class),
                $logger instanceof LoggerInterface ? $logger : new NullLogger(),
            );
        });
    }
}
