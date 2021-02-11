<?php

namespace Myerscode\Acorn;

use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Acorn\Framework\Helpers\FileService;
use Myerscode\Utilities\Bags\DotUtility;
use Myerscode\Utilities\Files\Exceptions\FileFormatExpection;
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
        $this->setBasePath($basePath);
        $this->setup();
    }

    protected function setup()
    {
        $this->container = new Container();

        $this->setupPaths();

        $this->bindAppEvents();

        $this->application = new Application($this->container(), $this->eventBus());

        $this->loadCommands();
    }

    protected function setupPaths()
    {
        $cwd = getcwd();

        $this->container->manager()->add('basePath', $this->basePath);

        $paths = [
            'base' => $this->basePath,
            'executing.dir.base' => $cwd,
            'executing.dir.app' => $cwd . '/app',
            'executing.dir.commands' => $cwd . '/app/Commands',
            'executing.dir.events' => $cwd . '/app/Events',
            'executing.dir.listeners' => $cwd . '/app/Listeners',
            'framework.dir.commands' => __DIR__.'/Foundation/Commands',
            'framework.dir.events' => __DIR__.'/Foundation/Events',
            'framework.dir.listeners' => __DIR__.'/Foundation/Listeners',
            'app.dir.commands' => $this->basePath.'/Commands',
            'app.dir.events' => $this->basePath.'/Events',
            'app.dir.listeners' => $this->basePath.'/Listeners',
        ];

        $this->container->manager()->add('paths', new DotUtility($paths));
    }

    /**
     * Runs the core application
     */
    public function run(): int
    {
        $input = $this->container->manager()->get(Input::class);

        $output = $this->container->manager()->get(Output::class);

        try {
            return $this->application->run($input, $output);
        } catch (CommandNotFoundException $exception) {
            $output->writeln($exception->getMessage());
        }

        return 1;
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

        $appEventListenersDirectory = path('app.dir.listeners');
        $coreEventListenersDirectory = path('framework.dir.listeners');

        /**
         * @var $fileService FileService
         */
        $fileService = $this->container->manager()->get(FileService::class);

        $eventDiscoveryDirs = [
            $coreEventListenersDirectory,
            $appEventListenersDirectory,
        ];

        foreach ($eventDiscoveryDirs as $directory) {
            foreach ($fileService->using($directory)->files() as $file) {
                /** @var  $file \Symfony\Component\Finder\SplFileInfo */
                $eventRegisterClass = $fileService->using($file->getRealPath())->fullyQualifiedClassname();
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
                        if (is_subclass_of($event, Event::class, true)) {
                            $this->eventBus()->addListener($event, $listener);
                        }
                    }
                }
            }
        }
    }

    protected function loadCommands()
    {
        $commandDirectory = path('app.dir.commands');

        /**
         * @var $fileService FileService
         */
        $fileService = $this->container->manager()->get(FileService::class);

        foreach ($fileService->using($commandDirectory)->files() as $file) {
            /** @var  $file \Symfony\Component\Finder\SplFileInfo */
            try {
                $commandClass = $fileService->using($file->getRealPath())->fullyQualifiedClassname();
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
    }
}
