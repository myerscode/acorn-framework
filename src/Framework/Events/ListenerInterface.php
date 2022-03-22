<?php

namespace Myerscode\Acorn\Framework\Events;

interface ListenerInterface
{
    public function shouldQueue(): bool;
}
