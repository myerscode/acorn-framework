<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Pipeline;

class PipeCollection implements PipeCollectionInterface
{
    /**
     * @param array<PipeInterface|string> $pipes
     */
    public function __construct(public readonly array $pipes)
    {
        //
    }

    /**
     * @return array<PipeInterface|string>
     */
    public function toArray(): array
    {
        return $this->pipes;
    }
}
