<?php

declare(strict_types=1);

namespace Myerscode\Acorn;

use Override;
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
use Myerscode\Acorn\Framework\Exceptions\CommandNotFoundException;
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
use Symfony\Component\Console\Exception\CommandNotFoundException as SymfonyCommandNotFoundException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Myerscode\Acorn\Foundation\config;

class Application extends SymfonyApplication
{
    public const string APP_NAME = 'Acorn';

    public const string APP_VERSION = '1.0.0';

    protected string $defaultCommand = 'list';

    /** @var array<string, string> */
    protected array $discoveredCommandDirectories = [];

    /** @var array<string, array<string, mixed>> */
    protected array $discoveredPackages = [];

    /** @var array<string, string> */
    protected array $discoveredProviders = [];

    /**
     * The logger instance.
     */
    protected LoggerInterface $logger;

    public function __construct(private readonly Container $container)
    {
        parent::__construct(self::APP_NAME, self::APP_VERSION);

        $this->setCatchExceptions(false);
        $this->setAutoExit(false);

        $this->bootstrap();
    }

    /**
     * Bootstrap the application: register providers, discover packages,
     * bind events, and load commands.
     *
     * Extracted from the constructor to make the boot sequence explicit
     * and testable. Called automatically during construction.
     */
    public function bootstrap(): void
    {
        $this->registerFrameworkProviders();

        $this->configureConsole();

        $this->discoverPackages();

        $this->loadServiceProviders();

        $this->bindAppEvents();

        $this->bindCommandEvents();

        $this->loadCommands();

        $this->setDefaultCommand($this->defaultCommand);
    }

    #[Override]
    public function addCommand(SymfonyCommand|callable $symfonyCommand): ?SymfonyCommand
    {
        if ($symfonyCommand instanceof Command || $symfonyCommand instanceof LoggerAwareInterface) {
            $symfonyCommand->setLogger($this->logger());
        }

        return parent::addCommand($symfonyCommand);
    }

    /**
     * @return string[]
     */
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

    /**
     * @return array<string, array<string, mixed>>
     */
    public function discoveredPackages(): array
    {
        return $this->discoveredPackages;
    }

    public function dispatcher(): Dispatcher
    {
        return $this->container()->get(Dispatcher::class);
    }

    /**
     * @return string[]
     */
    public function eventDiscoveryDirectories(): array
    {
        return array_filter([
            config('app.dir.listeners'),
            config('framework.dir.listeners'),
        ]);
    }

    public function handle(?InputInterface $input = null, ?OutputInterface $output = null): Result
    {
        $throwException = null;

        try {
            // this happens within the run, but we want to capture the SymfonyCommandNotFoundException and handle it ourselves
            $commandName = $input instanceof \Symfony\Component\Console\Input\InputInterface ? ($this->getCommandName($input) ?? $this->defaultCommand) : $this->defaultCommand;
            $this->find($commandName);
            $exitCode = parent::run($input, $output);
        } catch (SymfonyCommandNotFoundException $e) {
            throw new CommandNotFoundException($e->getMessage(), $e->getAlternatives(), $e->getCode(), $e);
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

    /**
     * @return string[]
     */
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
                    $eventRegisterClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                    try {
                        if (is_subclass_of($eventRegisterClass, Listener::class)) {
                            $listener = $this->container->get($eventRegisterClass);

                            $listensFor = $listener->listensFor();
                            if (is_string($listensFor)) {
                                $events = [$listensFor];
                            } elseif (is_array($listensFor)) {
                                $events = $listensFor;
                            } else {
                                $events = [];
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

    /**
     * Bind Symfony Console events to the Acorn dispatcher.
     *
     * The Acorn Dispatcher implements Symfony's EventDispatcherInterface, so it
     * can be used directly with Symfony Console. Console events (COMMAND, TERMINATE,
     * ERROR) are listened for and re-emitted as Acorn events for application listeners.
     */
    protected function bindCommandEvents(): void
    {
        $dispatcher = $this->dispatcher();

        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $consoleCommandEvent): void {
            $this->dispatcher()->emit(CommandBeforeEvent::class, $consoleCommandEvent);
        });

        $dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $consoleTerminateEvent): void {
            $this->dispatcher()->emit(CommandAfterEvent::class, $consoleTerminateEvent);
        });

        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $consoleErrorEvent): void {
            $this->dispatcher()->emit(CommandErrorEvent::class, $consoleErrorEvent);
        });

        $this->setDispatcher($dispatcher);
    }

    protected function configureConsole(): void
    {
        $this->configureIO($this->input(), $this->output());
    }

    /**
     * Look through installed packages and locate packages to load commands and services from
     */
    protected function discoverPackages(): void
    {
        $this->output()->debug(sprintf('Looking for packages in %s', config('app.root')));

        $packageDiscovery = new PackageDiscovery(config('app.root'));

        $this->discoveredPackages = $packageDiscovery->found;
        $this->discoveredCommandDirectories = $packageDiscovery->locateCommands();
        $this->discoveredProviders = $packageDiscovery->locateProviders();

        foreach (array_keys($packageDiscovery->found) as $package) {
            $this->output()->debug(sprintf('Discovered %s', $package));
        }
    }

    /**
     * Load commands from discovery directories.
     *
     * Performance note: This scans the filesystem and uses ReflectionClass on every boot.
     * For large applications, consider implementing a manifest cache (acorn:optimize)
     * that pre-scans and caches discovered class names.
     */
    protected function loadCommands(): void
    {
        $commandsDiscoveryDirectories = $this->commandsDiscoveryDirectories();
        $this->output()->debug(
            sprintf('Looking for commands in directories: %s', implode(', ', $commandsDiscoveryDirectories)),
        );
        foreach ($commandsDiscoveryDirectories as $commandDiscoveryDirectory) {
            $this->output()->debug(
                sprintf('Looking for commands in %s', $commandDiscoveryDirectory),
            );
            try {
                foreach (FileService::make($commandDiscoveryDirectory)->files() as $file) {
                    try {
                        $commandClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();
                        if (is_subclass_of($commandClass, Command::class, true) && new ReflectionClass($commandClass)->isInstantiable()) {
                            $this->addCommand($this->container->get($commandClass));
                            $this->output()->debug(
                                sprintf('Loaded %s from %s.', $commandClass, $commandDiscoveryDirectory),
                            );
                        } else {
                            $this->output()->debug(
                                sprintf('Found %s in %s, but did not load as was not a valid Command class', $commandClass, $commandDiscoveryDirectory),
                            );
                        }
                    } catch (FileFormatExpection|ReflectionException) {
                        $this->output()->debug(
                            sprintf('Unable to load %s from %s - unable to determine class name', $file->getRealPath(), $commandDiscoveryDirectory),
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
     * @return string[]
     */
    protected function providerDiscoveryDirectories(): array
    {
        return array_filter([
            config('app.dir.providers'),
            config('executing.dir.providers'),
            ...$this->discoveredProviders,
        ]);
    }

    /**
     * Load service providers from the framework into the container
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
     * @return string[]
     */
    protected function serviceProviders(): array
    {
        $providerDiscoveryDirectories = $this->providerDiscoveryDirectories();

        $serviceProviders = [];

        foreach ($providerDiscoveryDirectories as $providerDiscoveryDirectory) {
            try {
                $this->output()->debug(sprintf('Loading providers from %s', $providerDiscoveryDirectory));
                foreach (FileService::make($providerDiscoveryDirectory)->files() as $file) {
                    $providerClass = FileService::make($file->getRealPath())->fullyQualifiedClassname();

                    if (!class_exists($providerClass) || !is_subclass_of($providerClass, \Myerscode\Acorn\Framework\Providers\ServiceProvider::class)) {
                        $this->output()->debug(sprintf('Skipping invalid provider class: %s', $providerClass));
                        continue;
                    }

                    $serviceProviders[] = $providerClass;
                    $this->output()->debug(sprintf('Found Service Provider [%s]', $providerClass));
                }
            } catch (NotADirectoryException) {
                $this->output()->debug(sprintf('Could not find directory %s to load service providers from', $providerDiscoveryDirectory));
            }
        }

        return array_filter($serviceProviders);
    }
}
