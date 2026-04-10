<?php

namespace Myerscode\Acorn\Framework\Container;

use League\Container\ReflectionContainer;
use League\Container\ServiceProvider\ServiceProviderInterface;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use InvalidArgumentException;

class Container
{
    /**
     * The current globally available container (if any).
     */
    protected static ?Container $instance = null;

    protected array $loadedProviders = [];

    private readonly DependencyManager $dependencyManager;

    public function __construct()
    {
        $this->dependencyManager = new DependencyManager(new Definitions());
        $this->dependencyManager
            ->delegate(
                new ReflectionContainer(true),
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
    public function add(string $id, mixed $concrete = null, ?bool $shared = null): void
    {
        $this->dependencyManager->add($id, $concrete);
    }

    /**
     * Load a service provider into the container
     *
     * @param $serviceProvider
     */
    public function addServiceProvider(ServiceProviderInterface|string $serviceProvider): void
    {
        if (!is_subclass_of($serviceProvider, ServiceProvider::class)) {
            throw new InvalidArgumentException($serviceProvider . ' must be a ServiceProvider');
        }

        if (is_string($serviceProvider)) {
            $serviceProvider =  new $serviceProvider();
        }

        $serviceProviderName = $serviceProvider::class;


        if (!in_array($serviceProviderName, $this->loadedProviders)) {
            $this->loadedProviders[] = $serviceProviderName;
            $this->dependencyManager->addServiceProvider($serviceProvider);
        }
    }

    /**
     * Retrieve an instance from the container
     *
     *
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @see DependencyManager::get()
     */
    public function get(string $id): mixed
    {
        return $this->dependencyManager->get($id);
    }

    /**
     * Returns a list of service providers that have been added to the container
     */
    public function loadedProviders(): array
    {
        return $this->loadedProviders;
    }

    /**
     * Remove a service from the container
     *
     *
     *
     * @see DependencyManager::swap()
     */
    public function remove(string $id): void
    {
        $this->dependencyManager->remove($id);
    }

    /**
     * Swap the defined instance in the container with a new implementation
     *
     *
     * @see DependencyManager::swap()
     */
    public function swap(string $id, $concrete = null, ?bool $shared = null): void
    {
        $this->dependencyManager->swap($id, $concrete, $shared);
    }
}
