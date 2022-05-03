<?php

namespace Myerscode\Acorn\Framework\Container;

use League\Container\ReflectionContainer;

class Container
{
    /**
     * The current globally available container (if any).
     */
    protected static ?Container $instance = null;

    private readonly DependencyManager $container;

    public function __construct()
    {
        $this->container = new DependencyManager(new Definitions());
        $this->container
            ->delegate(
                (new ReflectionContainer())->cacheResolutions()
            )
            ->defaultToShared();

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
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Add an item to the container
     *
     * @see DependencyManager::add()
     */
    public function add(string $id, mixed $concrete = null, bool $shared = null): void
    {
        $this->container->add($id, $concrete, $shared);
    }

    /**
     * Retrieve an instance from the container
     *
     * @return mixed
     *
     * @see DependencyManager::get()
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * Swap the defined container with a new implementation
     *
     * @param  string  $id
     * @param  null  $concrete
     * @param  bool|null  $shared
     */
    public function swap(string $id, $concrete = null, bool $shared = null): void
    {
        $this->container->swap($id, $concrete, $shared);
    }

    /**
     * Load a service provider into the container
     *
     * @param $serviceProvider
     *
     * @return void
     */
    public function addServiceProvider($serviceProvider): void
    {
        $this->container->addServiceProvider($serviceProvider);
    }
}
