<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Contracts;

interface Arrayable
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array;
}
