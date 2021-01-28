<?php

namespace Myerscode\Acorn;

use League\Container\Container as DependencyManager;
use League\Container\ReflectionContainer;
use Myerscode\Acorn\Framework\Providers\ConsoleServiceProvider;

class Container
{
    /**
     * The current globally available container (if any).
     *
     * @var static
     */
    protected static $instance;

    private DependencyManager $container;

    public function __construct()
    {
        $this->container = new DependencyManager;
        $this->container->delegate(
            (new ReflectionContainer())->cacheResolutions()
        );

        $this->loadServiceProviders();
    }

    /**
     * Get the globally available instance of the container.
     *
     * @return static
     */
    public static function getInstance(): Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Retrieve an instance from the container
     *
     * @param  string  $id
     *
     * @return array|mixed|object
     */
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function manager(): DependencyManager
    {
        return $this->container;
    }

    protected function loadServiceProviders()
    {
        $serviceProviders = [
            ConsoleServiceProvider::class,
        ];

        foreach ($serviceProviders as $provider) {
            $this->manager()->addServiceProvider($provider);
        }
    }
}
