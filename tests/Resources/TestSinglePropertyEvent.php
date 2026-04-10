<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\Event;

class TestSinglePropertyEvent extends Event
{
    public function __construct(readonly string $numberWord)
    {
        //
    }
}
