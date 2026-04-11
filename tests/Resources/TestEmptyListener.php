<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

class TestEmptyListener extends Listener
{
    #[Override]
    public function handle(EventInterface $event): void
    {
        //
    }
}
