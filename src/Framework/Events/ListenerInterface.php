<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

interface ListenerInterface
{
    public function shouldQueue(): bool;
}
