<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Framework\Cache\Cache;
use Myerscode\Acorn\Framework\Cache\DriverInterface;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;

use function Myerscode\Acorn\Foundation\config;

class CacheProvider extends ServiceProvider
{
    protected $provides
        = [
            'cache',
            Cache::class,
            DriverInterface::class,
        ];

    public function register(): void
    {
        $this->getContainer()
            ->add(DriverInterface::class, config('app.cache.driver'))
            ->addArguments((is_array($cacheConfig = config('app.cache.config', []))) ? $cacheConfig : []);

        $this->getContainer()
            ->add(Cache::class)
            ->addArguments([DriverInterface::class, config('app.cache.namespace', 'acorn')]);

        $this->getContainer()->add('cache', fn() => $this->getContainer()->get(Cache::class));
    }
}
