<?php

namespace Myerscode\Acorn\Framework\Terminal;

class Command
{
    protected string $instructions;

    public function __construct(string|Command $command)
    {
        if (is_string($command)) {
            $this->setInstructions($command);
        } else {
            $this->setInstructions($command->instructions());
        }
    }

    public static function make(string|Command $command): Command
    {
        return new self($command);
    }

    public function instructions(): string
    {
        return $this->instructions;
    }

    private function setInstructions(string $instructions): void
    {
        $this->instructions = $instructions;
    }
}
