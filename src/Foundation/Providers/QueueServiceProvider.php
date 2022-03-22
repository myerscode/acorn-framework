<?php

namespace Myerscode\Acorn\Foundation\Providers;

use League\Container\Argument\RawArgument;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Queue\QueueInterface;

use function Myerscode\Acorn\Foundation\config;

class QueueServiceProvider extends AbstractServiceProvider
{
    protected $provides = [
        'queue',
        QueueInterface::class,
        Dispatcher::class
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $this->getContainer()->add(QueueInterface::class, config('app.queue'));

        $this->getContainer()->add('queue', function () {
            return $this->getContainer()->get(QueueInterface::class);
        });

        $this->getContainer()->add(Dispatcher::class, function () {
            return new Dispatcher($this->getContainer()->get(QueueInterface::class));
        });
    }
}
