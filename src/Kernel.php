<?php

namespace Myerscode\Acorn;

use Myerscode\Acorn\Framework\Config\Factory as ConfigFactory;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Symfony\Component\Console\Exception\CommandNotFoundException;

class Kernel
{
    /**
     * The base path for the Acorn application.
     */
    protected string $basePath;

    private Container $container;

    private Application $application;

    public function __construct(string $basePath = '')
    {
        $this->container = new Container();
        $this->setBasePath($basePath);
        $this->buildConfig();
        $this->application = new Application($this->container(), $this->eventBus());
    }

    protected function buildConfig()
    {
        $this->container->add('config', ConfigFactory::make([
            'base' => $this->basePath,
            'src' => __DIR__,
            'cwd' => getcwd(),
        ]));
    }

    public function input(): ConsoleInputInterface
    {
        return $this->container->manager()->get(Input::class);
    }

    public function output(): ConsoleOutputInterface
    {
        return $this->container->manager()->get(Output::class);
    }

    /**
     * Runs the core application
     */
    public function run(): int
    {
        try {
            $result = $this->application->run($this->input(), $this->output());

            // TODO if result failed but has no error do something
            if ($result->failed()) {
                throw $result->error();
            }

            return $result->exitCode();

        } catch (CommandNotFoundException $exception) {
            $this->output()->info($exception->getMessage());
        } catch (\Exception $exception) {
            $this->output()->error($exception->getMessage());
        }

        return 1;
    }

    /**
     * @return Application
     */
    public function application(): Application
    {
        return $this->application;
    }

    /**
     * @return Container
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * @return Dispatcher
     */
    public function eventBus(): Dispatcher
    {
        return $this->container()->manager()->get(Dispatcher::class);
    }


    protected function setBasePath(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->container->add('basePath', $this->basePath);
    }
}
