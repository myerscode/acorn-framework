<?php

namespace Myerscode\Acorn\Foundation\Console\Definition;

use Myerscode\Acorn\Framework\Console\CommandInterpreter;
use Symfony\Component\Console\Input\InputDefinition;

class SignatureInputDefinition extends InputDefinition
{
    public function __construct(string $signature = '')
    {
        parent::__construct([]);

        [, $arguments, $options] = (new CommandInterpreter())->parse($signature);

        $this->addArguments($arguments);
        $this->addOptions($options);
    }
}
