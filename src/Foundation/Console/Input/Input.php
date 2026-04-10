<?php

namespace Myerscode\Acorn\Foundation\Console\Input;

use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputDefinition;

class Input extends ArgvInput implements ConsoleInputInterface
{
    /** @var list<string> */
    protected array $argInput;

    /**
     * @param list<string>|null $argInput
     */
    public function __construct(array|null $argInput = null, ?InputDefinition $definition = null)
    {
        /** @var list<string> $argv */
        $argv = $argInput ?? ($_SERVER['argv'] ?? []);
        $this->argInput = $argv;

        parent::__construct($this->argInput, $definition);
    }

    /**
     * Get all parsed parameters given at input
     *
     * @return array<string, mixed>
     */
    public function parameters(): array
    {
        /** @var array<string, mixed> $params */
        $params = $this->argInput;

        return $params;
    }
}
