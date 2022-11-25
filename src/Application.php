<?php

namespace Myerscode\Acorn;

use Exception;
use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Config\PackageDiscovery;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Framework\Container\Container;
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
    /**
     * @var string
     */
    final const APP_NAME = 'Acorn';

    /**
     * @var string
     */
    final const APP_VERSION = '1.0.0';

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    protected array $discoveredPackages = [];

    protected array $discoveredCommandDirectories = [];

    protected array $discoveredProviders = [];

    public function __construct(private readonly Container $container)
    {
        $this->registerFrameworkProviders();

        $this->configureConsole();

        // allow command exceptions to bubble up and be handled by the kernel
        $this->setCatchExceptions(false);

        $this->setAutoExit(false);

        $this->discoverPackages();

        $this->loadServiceProviders();

        $this->bindAppEvents();

        $this->bindCommandEvents();

        $this->loadCommands();

        parent::__construct(self::APP_NAME, self::APP_VERSION);
    }

    public function add(SymfonyCommand $symfonyCommand): ?SymfonyCommand
    {
        if ($symfonyCommand instanceof Command || $symfonyCommand instanceof LoggerAwareInterface) {
            $symfonyCommand->setLogger($this->logger());
        }

        return parent::add($symfonyCommand);
    }

    public function commandsDiscoveryDirectories(): array
    {
        return array_filter([
            config('app.dir.commands'),
            config('framework.dir.commands'),
            ...$this->discoveredCommandDirectories,
        ]);
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function discoveredPackages(): array
    {
        return $this->discoveredPackages;
    }

    public function dispatcher(): Dispatcher
    {
        return $this->container()->get(Dispatcher::class);
    }

    public function eventDiscoveryDirectories(): array
    {
        return array_filter([
            config('app.dir.listeners'),
            config('framework.dir.listeners'),
        ]);
    }

    public function handle(InputInterface $input = null, OutputInterface $output = null): Result
    {
        $throwException = null;

        try {
            $exitCode = parent::run($input, $output);
        } catch (Exception $exception) {
            $throwException = $exception;
            $exitCode = 1;
        }

        return new Result($exitCode, $throwException);
    }

    public function input(): ConsoleInputInterface
    {
        return $this->container->get('input');
    }

    public function loadedServiceProviders(): array
    {
        return $this->container->loadedProviders();
    }

    /**
     * Get the logger instance.
     */
    public function logger(): LogInterface
    {
        return $this->container->get('logger');
    }

    public function output(): DisplayOutputInterface
    {
        return $this->container->get('output');
    }

    /**
     * Load events files and register them to the handler
     */
    protected function bindAppEvents(): void
    {
        $eventDiscoveryDirs = $this->eventDiscoveryDirectories();

        foreach ($eventDiscoveryDirs as $eventDiscoveryDir) {
            try {
                foreach (FileService::make($eventDiscoveryDir)->files() as $file) {
                    $this->output()->debug(sprintf('Loading events from %s to load events from', $eventDiscoveryDir));
                    /** @var  $file \Symfony\Component\Finder\SplFileInfo */
                    $eventRegisterClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                    try {
                        if (is_subclass_of($eventRegisterClass, Listener::class)) {
                            $listener = $this->container->get($eventRegisterClass);

                            $listensFor = $listener->listensFor();
                            if (is_string($listensFor)) {
                                $events = [$listensFor];
                            } elseif (is_array($listensFor)) {
                                $events = $listensFor;
                            }
                            foreach ($events as $event) {
                                $this->dispatcher()->addListener($event, $listener);
                            }
                        } else {
                            throw new InvalidListenerException(sprintf('%s is not a valid Listener', $eventRegisterClass));
                        }
                    } catch (InvalidListenerException $invalidListenerException) {
                        $this->output()->debug($invalidListenerException->getMessage());
                    }
                }
            } catch (NotADirectoryException) {
                $this->output()->debug(sprintf('Could not find directory %s to load events from', $eventDiscoveryDir));
            }
        }
    }

    protected function bindCommandEvents(): void
    {
        $eventDispatcher = new EventDispatcher;

        $eventDispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event): void {
            $this->dispatcher()->emit(CommandBeforeEvent::class, $event);
        });

        $eventDispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event): void {
            $this->dispatcher()->emit(CommandAfterEvent::class, $event);
        });

        $eventDispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event): void {
            $this->dispatcher()->emit(CommandErrorEvent::class, $event);
        });

        $this->setDispatcher($eventDispatcher);
    }

    protected function configureConsole(): void
    {
        $this->configureIO($this->input(), $this->output());
    }

    /**
     * Look through installed packages and locate packages to load commands and services from
     *
     * @return void
     */
    protected function discoverPackages(): void
    {
        $finder = new PackageDiscovery(config('app.root'));

        $this->discoveredPackages = $finder->found;
        $this->discoveredCommandDirectories = $finder->locateCommands();
        $this->discoveredProviders = $finder->locateProviders();

        foreach ($finder->found as $package => $meta) {
            $this->output()->debug(sprintf('Discovered %s', $package));
        }
    }

    protected function loadCommands(): void
    {
        $commandsDiscoveryDirectories = $this->commandsDiscoveryDirectories();

        foreach ($commandsDiscoveryDirectories as $commandDiscoveryDirectory) {
            try {
                foreach (FileService::make($commandDiscoveryDirectory)->files() as $file) {
                    try {
                        $commandClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                        if (is_subclass_of($commandClass, Command::class, true) && (new ReflectionClass($commandClass))->isInstantiable()) {
                            $this->add($this->container->get($commandClass));
                        } else {
                            $this->output()->debug(
                                sprintf('Found %s in %s, but did not load as was not a valid Command class', $commandClass, $commandDiscoveryDirectory)
                            );
                        }
                    } catch (FileFormatExpection|ReflectionException) {
                        $this->output()->debug(
                            sprintf('Unable to load %s from %s - unable to determine class name', $file->getRealPath(), $commandDiscoveryDirectory)
                        );
                    }
                }
            } catch (NotADirectoryException) {
                $this->output()->debug(sprintf('Could not find directory %s to load commands from', $commandDiscoveryDirectory));
            }
        }
    }

    /**
     * Load service providers from user land into the container
     *
     * @return void
     */
    protected function loadServiceProviders(): void
    {
        foreach ($this->serviceProviders() as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    /**
     * Look for userland providers
     *
     * @return array
     */
    protected function providerDiscoveryDirectories(): array
    {
        return array_filter([
            config('app.dir.providers'),
            ...$this->discoveredProviders,
        ]);
    }

    /**
     * Load service providers from the framework into the container
     *
     * @return void
     */
    protected function registerFrameworkProviders(): void
    {
        foreach (config('framework.providers', []) as $provider) {
            $this->container->addServiceProvider($provider);
        }
    }

    /**
     * Get list of service providers
     *
     * @return array
     */
    protected function serviceProviders(): array
    {
        $providerDiscoveryDirectories = $this->providerDiscoveryDirectories();

        $serviceProviders = [];

        foreach ($providerDiscoveryDirectories as $providerDirectory) {
            try {
                foreach (FileService::make($providerDirectory)->files() as $file) {
                    $serviceProviders[] = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                }
            } catch (NotADirectoryException) {
                $this->output()->debug(sprintf('Could not find directory %s to load service providers from', $providerDirectory));
            }
        }

        return array_filter($serviceProviders);
    }
}
