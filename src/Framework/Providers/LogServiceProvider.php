<?php

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Myerscode\Acorn\Framework\Log\NullLogger;

class LogServiceProvider extends AbstractServiceProvider
{
    /**
     * The provided array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        NullLogger::class,
        'logger',
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $this->getContainer()->add(NullLogger::class);
        $this->getContainer()->add('logger', function () {
            return $this->getContainer()->get(NullLogger::class);
        });
    }
}
