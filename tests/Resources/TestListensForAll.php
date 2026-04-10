<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Listener;

class TestListensForAll extends Listener
{
    /**
     * @var string[]|string
     */
    #[\Override]
    protected string|array $listensFor = '*';

    public function handle(TestEvent $testEvent): void
    {
        //
    }
}
