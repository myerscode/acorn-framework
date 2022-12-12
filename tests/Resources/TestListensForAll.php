<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Listener;

class TestListensForAll extends Listener
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = '*';

    public function handle(TestEvent $testEvent): void
    {
        //
    }
}
