<?php

namespace Myerscode\Acorn\Foundation\Console\Input;

use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

class Input extends ArgvInput implements ConsoleInputInterface
{
    public function __construct(protected array|null $argInput = [], InputDefinition $definition = null)
    {
        parent::__construct($argInput, $definition);
    }

    /**
     * Get all parsed parameters given at input
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->argInput ?? [];
    }
}
