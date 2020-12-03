<?php

namespace Myerscode\Acorn\Framework\Events;

use League\Event\AbstractListener;

abstract class AcornEventListener extends AbstractListener
{

    protected array $listensFor = [];

    public function listensFor(): array
    {
        return $this->listensFor;
    }
}
