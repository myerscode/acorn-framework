<?php

namespace Myerscode\Acorn\Framework\Container;

use League\Container\Container;
use League\Container\Definition\DefinitionInterface;

class DependencyManager extends Container
{

    public function remove(string $id): void
    {
        $this->definitions->remove($id);
    }

    public function swap(string $id, $concrete = null, bool $shared = null): DefinitionInterface
    {
        if ($this->has($id)) {
            $this->remove($id);
        }

        return $this->add($id, $concrete, $shared);
    }
}
