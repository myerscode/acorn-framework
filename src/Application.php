<?php

namespace Myerscode\Acorn;

use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Acorn\Framework\Events\Exception\InvalidListenerException;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Acorn\Framework\Log\LogInterface;
use Myerscode\Utilities\Files\Exceptions\FileFormatExpection;
use Myerscode\Utilities\Files\Exceptions\NotADirectoryException;
use Myerscode\Utilities\Files\Utility as FileService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use function Myerscode\Acorn\Foundation\config;

class Application extends SymfonyApplication
{
    const APP_NAME = 'Acorn';

    const APP_VERSION = '1.0.0';

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Container
     */
    private Container $container;

    private Dispatcher $event;

    public function __construct(Container $container, Dispatcher $event)
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->event = $event;

        // allow command exceptions to bubble up and be handled by the kernel
        $this->setCatchExceptions(false);

        $this->setAutoExit(false);

        $this->container = $container;

        $this->bindAppEvents();

        $this->bindCommandEvents();

        $this->loadCommands();
    }

    protected function bindCommandEvents()
    {
        $dispatcher = new EventDispatcher;

        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            $this->event->emit(CommandBeforeEvent::class, $event);
        });

        $dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
            $this->event->emit(CommandAfterEvent::class, $event);
        });

        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event) {
            $this->event->emit(CommandErrorEvent::class, $event);
        });

        $this->setDispatcher($dispatcher);
    }

    /**
     * return string[]
     */
    public function eventDiscoveryDirectories(): array
    {
        return [
            config('app.dir.listeners'),
            config('framework.dir.listeners'),
        ];
    }

    /**
     * Load events files and register them to the handler
     */
    protected function bindAppEvents(): void
    {
        $eventDiscoveryDirs = $this->eventDiscoveryDirectories();

        $output = $this->container->manager()->get(Output::class);

        foreach ($eventDiscoveryDirs as $directory) {
            try {
                foreach (FileService::make($directory)->files() as $file) {
                    $output->debug("Loading events from $directory to load events from");
                    /** @var  $file \Symfony\Component\Finder\SplFileInfo */
                    $eventRegisterClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                    try {
                        if (is_subclass_of($eventRegisterClass, Listener::class, true)) {
                            $listener = $this->container->manager()->get($eventRegisterClass);
                            $listensFor = $listener->listensFor();
                            if (is_string($listensFor)) {
                                $events = [$listensFor];
                            } elseif (is_array($listensFor)) {
                                $events = $listensFor;
                            } else {
                                throw new InvalidListenerException("$eventRegisterClass contains invalid listener configuration");
                            }
                            foreach ($events as $event) {
                                $this->event->addListener($event, $listener);
                            }
                        }
                    } catch (InvalidListenerException $invalidListenerException) {
                        $output->debug($invalidListenerException->getMessage());
                    }
                }
            } catch (NotADirectoryException $notADirectoryException) {
                $output->debug("Could not find directory $directory to load events from");
            }
        }
    }

    /**
     * return string[]
     */
    public function commandsDiscoveryDirectories(): array
    {
        return [
            config('app.dir.commands'),
        ];
    }


    protected function loadCommands()
    {
        $commandsDiscoveryDirectories = $this->commandsDiscoveryDirectories();

        $output = $this->container->manager()->get(Output::class);

        foreach ($commandsDiscoveryDirectories as $commandDirectory) {
            try {
                foreach (FileService::make($commandDirectory)->files() as $file) {
                    try {
                        $commandClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                        if (is_subclass_of($commandClass, Command::class, true) && (new ReflectionClass($commandClass))->isInstantiable()) {
                            $this->add($this->container->manager()->get($commandClass));
                        } else {
                            $output->debug("Found $commandClass in $commandDirectory, but did not load as was not a valid Command class");
                        }
                    } catch (FileFormatExpection | ReflectionException $e) {
                        $output->debug("Unable to load {$file->getRealPath()} from $commandDirectory - unable to determine class name");
                    }
                }
            } catch (NotADirectoryException $notADirectoryException) {
                $output->debug("Could not find directory $commandDirectory to load commands from");
            }
        }
    }

    public function add(SymfonyCommand $command)
    {

        if ($command instanceof Command || $command instanceof LoggerAwareInterface) {
            $command->setLogger($this->logger());
        }

        return parent::add($command);
    }

    /**
     * Get the logger instance.
     *
     * @return LogInterface
     */
    public function logger(): LogInterface
    {
        return $this->container->manager()->get('logger');
    }

    public function run(InputInterface $input = null, OutputInterface $output = null): Result
    {
        $throwException = null;

        try {
            $exitCode = parent::run($input, $output);
        } catch (\Exception $exception) {
            $throwException = $exception;
            $exitCode = 1;
        }

        return new Result($exitCode, $throwException);
    }
}
