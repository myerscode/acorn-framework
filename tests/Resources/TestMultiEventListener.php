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
    public function handle(EventInterface $event): void
    {
        //
    }

    public function listensFor(): array|string
    {
        return [
        TestEvent::class,
        'another.test.event',
    ];
    }
}
