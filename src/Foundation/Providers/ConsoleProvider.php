<?php

namespace Myerscode\Acorn\Foundation\Providers;

use Myerscode\Acorn\Foundation\Console\Input;
use Myerscode\Acorn\Foundation\Console\Output;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleProvider extends ServiceProvider
{
    protected $provides = [
        Command::class,
        Input::class,
        Output::class,
        'input',
        'output',
    ];

    public function register(): void
    {
        $this->getContainer()->add(Input::class);
        $this->getContainer()->add('input', fn() => $this->getContainer()->get(Input::class));
        $this->getContainer()->add(Output::class)->addArguments([ Input::class, ConsoleOutput::class]);
        $this->getContainer()->add('output', fn() => $this->getContainer()->get(Output::class));
    }
}
