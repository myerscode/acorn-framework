<?php

declare(strict_types=1);

namespace Tests\Resources\App\Listeners;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class ListensForArrayListener extends Listener
{
    /**
     * @var string[]
     */
    #[Override]
    protected string|array $listensFor = [
        'test.event.one',
        'test.event.two',
    ];

    #[Override]
    public function handle(EventInterface $event): void
    {
        //
    }
}
