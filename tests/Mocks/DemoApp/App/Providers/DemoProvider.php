<?php

declare(strict_types=1);

namespace Tests\Mocks\DemoApp\App\Providers;

use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Override;

class DemoProvider extends ServiceProvider
{
    #[Override]
    protected $provides = [
        'demo',
    ];

    public function register(): void
    {
        $this->getContainer()->add('demo', static fn (): string => 'Demo Provider');
    }
}
