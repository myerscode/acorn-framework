<?php

namespace Myerscode\Acorn\Framework\Pipeline;

class PipeCollection implements PipeCollectionInterface
{
    public function __construct(readonly array $pipes)
    {
        //
    }

    public function toArray(): array
    {
        return $this->pipes;
    }
}
