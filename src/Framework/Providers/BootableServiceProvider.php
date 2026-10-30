<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\BootableServiceProviderInterface;

abstract class BootableServiceProvider extends ServiceProvider implements BootableServiceProviderInterface
{
    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     */
    abstract public function boot(): void;
}
