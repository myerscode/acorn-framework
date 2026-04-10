<?php

declare(strict_types=1);

namespace App\Providers;

use Myerscode\Acorn\Framework\Providers\ServiceProvider;

class DemoProvider extends ServiceProvider
{
    #[\Override]
    protected $provides = [ ];

    public function register(): void
    {
        //
    }
}
