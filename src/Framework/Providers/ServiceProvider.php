<?php

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

abstract class ServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string[]
     */
    protected $provides = [];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    abstract public function register(): void;

    public function provides(string $id): bool
    {
        return in_array($id, $this->provides);
    }
}
