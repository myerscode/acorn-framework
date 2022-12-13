<?php

namespace Myerscode\Acorn\Foundation\Console\Input;

use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class ConfigInput extends ArrayInput implements ConsoleInputInterface
{
    public function __construct(protected array $parameterArray = [], InputDefinition $inputDefinition = null)
    {
        parent::__construct($parameterArray, $inputDefinition);
    }

    /**
     * Get all parsed parameters given at input
     *
     * @return array
     */
    public function parameters(): array
    {
        return $this->parameterArray;
    }
}
