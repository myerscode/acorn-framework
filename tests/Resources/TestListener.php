<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class TestListener extends Listener
{
    /**
     * @var string[]|string
     */
    #[\Override]
    protected string|array $listensFor = TestEvent::class;

    public function handle(TestEvent $testEvent): void
    {
        //
    }
}
