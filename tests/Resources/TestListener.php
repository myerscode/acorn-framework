<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class TestListener extends Listener
{
    /**
     * @var string[]|string
     */
    #[Override]
    protected string|array $listensFor = TestEvent::class;

    #[Override]
    public function handle(EventInterface $event): void
    {
        //
    }
}
