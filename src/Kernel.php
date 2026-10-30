<?php

declare(strict_types=1);

namespace Myerscode\Acorn;

use Exception;
use RuntimeException;
use Myerscode\Acorn\Foundation\Exceptions\ExceptionHandler;
use Myerscode\Acorn\Framework\Config\Manager;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Framework\Container\Container;
use Myerscode\Acorn\Framework\Exceptions\CommandNotFoundException;
use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;

class Kernel
{
    /**
     * The base path for the Acorn application.
     */
    protected string $basePath;

    protected string $rootPath;

    private ?Application $application = null;

    private bool $booted = false;

    private ?Manager $configManager = null;

    private readonly Container $container;

    public function __construct(string $basePath = '')
    {
        $this->container = new Container();
        $this->setBasePath($basePath);
        $this->setRootPath($basePath);
    }

    public function application(): Application
    {
        return $this->boot()->application ?? throw new RuntimeException('Application not booted');
    }

    public function config(): Manager
    {
        return $this->configManager ??= new Manager($this->rootPath);
    }

    public function container(): Container
    {
        return $this->container;
    }

    public function input(): ConsoleInputInterface
    {
        return $this->boot()->application()->input();
    }

    public function output(): DisplayOutputInterface
    {
        return $this->boot()->application()->output();
    }

    /**
     * Runs the core application
     */
    public function run(): int
    {
        try {
            $result = $this->processCommand();

            if ($result->failed() && $result->error() instanceof Exception) {
                throw $result->error();
            }

            return $result->exitCode();
        } catch (CommandNotFoundException $commandNotFoundException) {
            $this->exceptionHandler()->report($commandNotFoundException);
            $this->exceptionHandler()->render($this->output(), $commandNotFoundException);
        } catch (\Throwable $exception) {
            $this->exceptionHandler()->report($exception);
            $this->exceptionHandler()->render($this->output(), $exception);
        }

        return 1;
    }

    /**
     * Get the exception handler instance.
     */
    public function exceptionHandler(): ExceptionHandlerInterface
    {
        try {
            return $this->container->get(ExceptionHandlerInterface::class);
        } catch (\Throwable) {
            // Fallback if container isn't ready yet or handler not registered
            return new ExceptionHandler(new \Myerscode\Acorn\Framework\Log\NullLogger());
        }
    }

    /**
     * Load the config and boot the application
     *
     * @return $this
     */
    protected function boot(): self
    {
        if ($this->booted) {
            return $this;
        }

        $this->loadConfig();

        $this->application = new Application($this->container());

        $this->booted = true;

        return $this;
    }

    /**
     * @return string[]
     */
    protected function configLocations(): array
    {
        return [
            __DIR__ . '/Config',
            $this->basePath . '/Config',
        ];
    }

    protected function loadConfig(): void
    {
        $configManager = $this->config();

        $config = $configManager->loadConfig($this->configLocations(), [
            'base' => $this->basePath,
            'root' => $this->rootPath,
            'src' => __DIR__,
            'cwd' => getcwd(),
        ]);

        $this->container->add('config', $config);
    }

    protected function processCommand(): Result
    {
        return $this->boot()->application()->handle($this->input(), $this->output());
    }

    protected function setBasePath(string $basePath): void
    {
        $this->basePath = rtrim($basePath, '\/');
        $this->container->add('basePath', $this->basePath);
    }

    protected function setRootPath(string $basePath): void
    {
        $this->rootPath = dirname(rtrim($basePath, '\/'));
        $this->container->add('rootPath', $this->rootPath);
    }
}
