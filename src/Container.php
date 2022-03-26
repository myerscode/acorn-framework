<?php

namespace Myerscode\Acorn;

use League\Container\Container as DependencyManager;
use League\Container\ReflectionContainer;
use Myerscode\Acorn\Foundation\Providers\QueueServiceProvider;
use Myerscode\Acorn\Framework\Providers\ConsoleServiceProvider;
use Myerscode\Acorn\Framework\Providers\LogServiceProvider;

class Container
{
    /**
     * The current globally available container (if any).
     */
    protected static ?Container $instance = null;

    private readonly DependencyManager $container;

    public function __construct()
    {
        $this->container = new DependencyManager;
        $this->container
            ->delegate(
                (new ReflectionContainer())->cacheResolutions()
            )
            ->defaultToShared();

        $this->loadServiceProviders();

        static::$instance = $this;
    }

    public static function flush(): void
    {
        static::$instance = null;
    }

    /**
     * Get the globally available instance of the container.
     */
    public static function getInstance(): Container
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Add an item to the container
     *
     * @param  mixed  $concrete
     *
     * @see DependencyManager::add()
     */
    public function add(string $id, $concrete = null, bool $shared = null): void
    {
        $this->container->add($id, $concrete, $shared);
    }

    /**
     * Retrieve an instance from the container
     *
     * @return array|mixed|object
     * @see DependencyManager::get()
     */
    public function get(string $id)
    {
        return $this->container->get($id);
    }

    public function manager(): DependencyManager
    {
        return $this->container;
    }

    protected function loadServiceProviders(): void
    {
        $serviceProviders = [
            ConsoleServiceProvider::class,
            LogServiceProvider::class,
            QueueServiceProvider::class,
        ];

        foreach ($serviceProviders as $serviceProvider) {
            $this->manager()->addServiceProvider($serviceProvider);
        }
    }
}
