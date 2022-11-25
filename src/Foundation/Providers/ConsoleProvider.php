<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Console\Input\Input;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleProvider extends ServiceProvider
{
    protected $provides
        = [
            DisplayOutput::class,
            Input::class,
            'input',
            'output',
        ];

    public function register(): void
    {
        $this->getContainer()->add(Input::class);
        $this->getContainer()->add('input', fn() => $this->getContainer()->get(Input::class));
        $this->getContainer()->add(DisplayOutput::class)->addArguments([Input::class, ConsoleOutput::class]);
        $this->getContainer()->add('output', fn() => $this->getContainer()->get(DisplayOutput::class));
    }
}
