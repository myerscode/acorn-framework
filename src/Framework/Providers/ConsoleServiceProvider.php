<?php

namespace Myerscode\Acorn\Framework\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Bus;
use Myerscode\Acorn\Framework\Events\Emitter;

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
        'input',
        Input::class,
        'output',
        Output::class,
    ];

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register()
    {
        $input = $this->getContainer()->add(Input::class);
        $this->getContainer()->add('input', $input);
        $output = $this->getContainer()->add(Output::class);
        $this->getContainer()->add('output', $output);

    }

    public function boot()
    {
        $this->getContainer()
            ->inflector(Command::class)
            ->invokeMethod('setContainer', [$this->getContainer()]);
    }
}
