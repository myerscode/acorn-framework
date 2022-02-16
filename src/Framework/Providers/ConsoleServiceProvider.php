<?php

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * The provided array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        Command::class,
        Input::class,
        Output::class,
        'input',
        'output',
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $this->getContainer()->add(Input::class);
        $this->getContainer()->add('input', function () {
            return $this->getContainer()->get(Input::class);
        });
        $this->getContainer()->add(Output::class)->addArguments([Input::class, ConsoleOutput::class]);
        $this->getContainer()->add('output', function () {
            return $this->getContainer()->get(Output::class);
        });
    }

    public function boot()
    {
        //
    }
}
