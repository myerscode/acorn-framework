<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Console\Input;

use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class ConfigInput extends ArrayInput implements ConsoleInputInterface
{
    /**
     * @param array<string, mixed> $parameterArray
     */
    public function __construct(protected array $parameterArray = [], ?InputDefinition $inputDefinition = null)
    {
        parent::__construct($parameterArray, $inputDefinition);
    }

    /**
     * Get all parsed parameters given at input
     *
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        return $this->parameterArray;
    }
}
