<?php

namespace Myerscode\Acorn;

use League\Container\Container as DependencyManager;
use League\Container\ReflectionContainer;
use Myerscode\Acorn\Framework\Providers\ConsoleServiceProvider;
use Myerscode\Acorn\Framework\Providers\EventServiceProvider;
use Myerscode\Acorn\Framework\Providers\HelperServiceProvider;

class Container
{
    private DependencyManager $container;

    public function __construct()
    {
        $this->container = new DependencyManager;
        $this->container->delegate(
            (new ReflectionContainer())->cacheResolutions()
        );

        $this->loadServiceProviders();
    }

    public function manager(): DependencyManager
    {
        return $this->container;
    }

    protected function loadServiceProviders()
    {
        $serviceProviders = [
            ConsoleServiceProvider::class,
            EventServiceProvider::class,
            HelperServiceProvider::class,
        ];

        foreach ($serviceProviders as $provider) {
            $this->manager()->addServiceProvider($provider);
        }
    }
}
