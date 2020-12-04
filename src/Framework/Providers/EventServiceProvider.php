<?php

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Myerscode\Acorn\Framework\Events\Bus;
use Myerscode\Acorn\Framework\Events\Emitter;
use Myerscode\Acorn\Framework\Events\Planner;

class EventServiceProvider extends AbstractServiceProvider
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
        Bus::class,
        Planner::class,
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $this->getContainer()
            ->add(Bus::class)
            ->addArgument($this->getContainer()->get(Emitter::class));

        $this->getContainer()
            ->add(Planner::class)
            ->addArgument($this->getContainer())
            ->addArgument($this->getContainer()->get(Bus::class));
    }
}