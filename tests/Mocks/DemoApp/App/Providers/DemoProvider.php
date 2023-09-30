<?php

namespace Tests\Mocks\DemoApp\App\Providers;

use Myerscode\Acorn\Framework\Providers\ServiceProvider;

class DemoProvider extends ServiceProvider
{
    protected $provides = [
        'demo',
    ];

    public function register(): void
    {
        $this->getContainer()->add('demo', static fn(): string => "Demo Provider");
    }
}
