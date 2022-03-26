<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Subscriber;

class TestSubscriber extends Subscriber
{
    protected array $events = [
        'foo' => 'onFoo',
        'bar' => 'onBar',
    ];

    public function onFoo(): bool
    {
        return true;
    }

    public function onBar(): bool
    {
        return true;
    }
}
