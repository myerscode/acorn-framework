<?php

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Input\InputInterface;

interface ConsoleInputInterface extends InputInterface
{
    /**
     * Get all parsed parameters given at input
     */
    public function parameters(): array;
}
