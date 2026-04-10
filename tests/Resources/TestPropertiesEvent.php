<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;

class TestPropertiesEvent extends Event
{
    public function __construct(public readonly int $number, public readonly string $word)
    {
        //
    }
}
