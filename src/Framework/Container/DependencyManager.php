<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Container;

use League\Container\Container;
use League\Container\Definition\DefinitionInterface;

class DependencyManager extends Container
{
    public function remove(string $id): void
    {
        // league/container 5 does not support removal; overwrite with null to effectively reset
        if ($this->has($id)) {
            $this->add($id, null, true);
        }
    }

    public function swap(string $id, mixed $concrete = null, ?bool $shared = null): DefinitionInterface
    {
        return $this->add($id, $concrete, true);
    }
}
