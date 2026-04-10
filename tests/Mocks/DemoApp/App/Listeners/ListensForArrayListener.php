<?php

declare(strict_types=1);

namespace Tests\Mocks\DemoApp\App\Listeners;

use Myerscode\Acorn\Framework\Events\Listener;

class ListensForArrayListener extends Listener
{
    /**
     * @var string[]
     */
    #[\Override]
    protected string|array $listensFor = [
        'test.event.one',
        'test.event.two',
    ];
}
