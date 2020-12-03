<?php

namespace Myerscode\Acorn;

use League\CLImate\CLImate;
use Myerscode\Acorn\Foundation\CoreEventRegister;
use Myerscode\Acorn\Framework\Console\AcornCommand;
use Myerscode\Acorn\Framework\Events\AcornEvent;
use Myerscode\Acorn\Framework\Events\AcornEventListener;
use Myerscode\Acorn\Framework\Events\AcornEventRegister;
use Myerscode\Acorn\Framework\Events\Bus;
use Myerscode\Acorn\Framework\Events\Planner;
use Myerscode\Acorn\Framework\Exception\Handler as ErrorHandler;
use Myerscode\Acorn\Framework\Helpers\Files\FileService;

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
        new ErrorHandler();
        $this->container = new Container();

        $this->container->manager()->add('basePath', $this->basePath);

        $this->bindAppEvents();

        $this->application = new Application($this->container(), $this->eventBus());

        $this->loadCommands();
    }

    /**
     * Runs the core application
     */
    public function run(): int
    {
        $input = $this->container->manager()->get('input');
        $output = $this->container->manager()->get('output');

        return $this->application->run($input, $output);
    }

    /**
     * @return Container
     */
    public function container(): Container
    {
        return $this->container;
    }

    /**
     * @return Bus
     */
    public function eventBus(): Bus
    {
        return $this->container->manager()->get(Bus::class);
    }

    /**
     * Load events files and register them to the handler
     */
    protected function bindAppEvents(): void
    {

        $planner = $this->container->manager()->get(Planner::class);

        $planner->bindEventsFromRegister(new CoreEventRegister);

        $eventsDirectory = $this->container->manager()->get('basePath') .'/Listeners';

        /**
         * @var $directoryService FileService
         */
        $directoryService = $this->container->manager()->get(FileService::class);

        foreach ($directoryService->filesIn($eventsDirectory) as $file) {
            /** @var  $file \Symfony\Component\Finder\SplFileInfo */
            $eventRegisterClass = $directoryService->getFullyQualifiedClassname($file->getRealPath());
            if (is_subclass_of($eventRegisterClass, AcornEventListener::class, true)) {
                $planner->bindEventFromListener($this->container->manager()->get($eventRegisterClass));
            }
        }
    }

    protected function loadCommands()
    {
        $commandDirectory = $this->container->manager()->get('basePath') .'/Commands';

        /**
         * @var $directoryService FileService
         */
        $directoryService = $this->container->manager()->get(FileService::class);

        foreach ($directoryService->filesIn($commandDirectory) as $file) {
            /** @var  $file \Symfony\Component\Finder\SplFileInfo */
            $commandClass = $directoryService->getFullyQualifiedClassname($file->getRealPath());

            if (is_subclass_of($commandClass, AcornCommand::class, true)) {
                $this->application->add($this->container->manager()->get($commandClass));
            }
        }
    }

    protected function setBasePath(string $basePath)
    {
        $this->basePath = rtrim($basePath, '\/');
    }
}
