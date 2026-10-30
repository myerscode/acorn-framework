<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Container;

use League\Container\Container;
use League\Container\Definition\DefinitionInterface;

class DependencyManager extends Container
{
    /**
     * Remove a binding from the container entirely.
     *
     * After removal, calling get($id) will attempt auto-wiring via the
     * delegate (ReflectionContainer) or throw a NotFoundException.
     */
    public function remove(string $id): void
    {
        if ($this->definitions instanceof Definitions) {
            $this->definitions->remove($id);
        }
    }

    public function swap(string $id, mixed $concrete = null, ?bool $shared = null): DefinitionInterface
    {
        return $this->add($id, $concrete, true);
    }
}
