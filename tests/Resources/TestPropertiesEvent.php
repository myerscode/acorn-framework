<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;

class TestPropertiesEvent extends Event
{
    public function __construct(readonly int $number, readonly string $word)
    {
        //
    }
}
