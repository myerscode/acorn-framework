<?php

namespace Myerscode\Acorn\Framework\Container;

use League\Container\ReflectionContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container
{
    /**
     * The current globally available container (if any).
     */
    protected static ?Container $instance = null;
    protected array $loadedProviders = [];
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
     * Load a service provider into the container
     *
     * @param $serviceProvider
     *
     * @return void
     */
    public function addServiceProvider($serviceProvider): void
    {
        if (!in_array($serviceProvider, $this->loadedProviders)) {
            $this->loadedProviders[] = $serviceProvider;
            $this->container->addServiceProvider($serviceProvider);
        }
    }

    /**
     * Retrieve an instance from the container
     *
     * @param  string  $id
     *
     * @return mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @see DependencyManager::get()
     */
    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    /**
     * Returns a list of service providers that have been added to the container
     *
     * @return array
     */
    public function loadedProviders(): array
    {
        return $this->loadedProviders;
    }

    /**
     * Remove a service from the container
     *
     * @param  string  $id
     *
     * @return void
     *
     * @see DependencyManager::swap()
     */
    public function remove(string $id): void
    {
        $this->container->remove($id);
    }

    /**
     * Swap the defined instance in the container with a new implementation
     *
     * @param  string  $id
     * @param  null  $concrete
     * @param  bool|null  $shared
     *
     * @see DependencyManager::swap()
     */
    public function swap(string $id, $concrete = null, bool $shared = null): void
    {
        $this->container->swap($id, $concrete, $shared);
    }
}
