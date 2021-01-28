<?php

namespace Myerscode\Acorn\Framework\Events;

interface EventInterface
{

    public function isPropagationStopped(): bool;

    public function stopPropagation(): self;

}
