<?php

namespace Myerscode\Acorn;

use Myerscode\Acorn\Framework\Config\Factory as ConfigFactory;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Files\Exceptions\FileFormatExpection;
use Myerscode\Utilities\Files\Utility as FileService;
use ReflectionClass;
use ReflectionException;
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
        $this->setup();
    }

    protected function setup()
    {

        $this->buildConfig();

        $this->bindAppEvents();

        $this->application = new Application($this->container(), $this->eventBus());

        $this->loadCommands();
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
            return $this->application->run($this->input(), $this->output());
        } catch (CommandNotFoundException $exception) {
            $this->output()->warning($exception->getMessage());
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
        return $this->container->manager()->get(Dispatcher::class);
    }

    /**
     * Load events files and register them to the handler
     */
    protected function bindAppEvents(): void
    {
        $eventDiscoveryDirs = [
            config('app.dir.listeners'),
            config('framework.dir.listeners'),
        ];

        foreach ($eventDiscoveryDirs as $directory) {
            foreach (FileService::make($directory)->files() as $file) {
                /** @var  $file \Symfony\Component\Finder\SplFileInfo */
                $eventRegisterClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                if (is_subclass_of($eventRegisterClass, Listener::class, true)) {
                    $listener = $this->container->manager()->get($eventRegisterClass);
                    $listensFor = $listener->listensFor();
                    $events = [];
                    if (is_string($listensFor)) {
                        $events = [$listensFor];
                    } elseif (is_array($listensFor)) {
                        $events = $listensFor;
                    }
                    foreach ($events as $event) {
                        $this->eventBus()->addListener($event, $listener);
                    }
                }
            }
        }
    }

    protected function loadCommands()
    {
        $commandDirectory = config('app.dir.commands');

        foreach (FileService::make($commandDirectory)->files() as $file) {
            try {
                $commandClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                if (is_subclass_of($commandClass, Command::class, true) && (new ReflectionClass($commandClass))->isInstantiable()) {
                    $this->application->add($this->container->manager()->get($commandClass));
                }
            } catch (FileFormatExpection | ReflectionException $e) {
                // TODO log output in -vvv mode
            }
        }
    }

    protected function setBasePath(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->container->add('basePath', $this->basePath);
    }
}
