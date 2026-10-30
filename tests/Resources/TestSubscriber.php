<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Subscriber;
use Override;

class TestSubscriber extends Subscriber
{
    #[Override]
    protected array $events = [
        'foo' => 'onFoo',
        'bar' => 'onBar',
    ];

    public function onBar(): bool
    {
        return true;
    }

    public function onFoo(): bool
    {
        return true;
    }
}
