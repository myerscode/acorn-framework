<?php

namespace Myerscode\Acorn\Foundation\Console;

use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;

class ConfigInput extends ArrayInput implements ConsoleInputInterface
{
    public function __construct(array $parameters =[], InputDefinition $definition = null)
    {
        parent::__construct($parameters, $definition);
    }
}
