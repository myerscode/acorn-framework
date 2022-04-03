<?php

namespace Myerscode\Acorn\Framework\Container;

use League\Container\Definition\DefinitionAggregate;

class Definitions extends DefinitionAggregate
{

    public function remove(string $id): void
    {
        foreach ($this->definitions as $index => $definition) {
            if ($definition->getAlias() === $id) {
                unset($this->definitions[$index]);
            }
        }
        $this->definitions = array_values($this->definitions);
    }
}
