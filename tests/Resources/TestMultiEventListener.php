<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class TestMultiEventListener extends Listener
{
    /**
     * @var string[]|string
     */
    #[Override]
    protected string|array $listensFor = [
        TestEvent::class,
        'another.test.event',
    ];

    #[Override]
    public function handle(EventInterface $event): void
    {
        //
    }
}
