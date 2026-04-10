<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class BootableServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * @var string[]
     */
    protected $provides = [];

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     */
    abstract public function boot(): void;

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    abstract public function register(): void;
}
