<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Pipeline;

class PipeCollection implements PipeCollectionInterface
{
    public function __construct(public readonly array $pipes)
    {
        //
    }

    public function toArray(): array
    {
        return $this->pipes;
    }
}
