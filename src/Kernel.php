<?php

namespace Myerscode\Acorn;

use Exception;
use Myerscode\Acorn\Framework\Config\Manager;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Console\Result;
use Myerscode\Acorn\Framework\Container\Container;
use Myerscode\Acorn\Framework\Exceptions\CommandNotFoundException;

class Kernel
{
    /**
     * The base path for the Acorn application.
     */
    protected string $basePath;

    protected string $rootPath;

    private readonly Container $container;

    private readonly Application $application;

    private bool $booted;

    public function __construct(string $basePath = '')
    {
        $this->booted = false;
        $this->container = new Container();
        $this->setBasePath($basePath);
        $this->setRootPath($basePath);
    }

    public function application(): Application
    {
        return $this->boot()->application;
    }

    public function config(): Manager
    {
        return new Manager($this->rootPath);
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

            // TODO if result failed but has no error do something
            if ($result->failed()) {
                throw $result->error();
            }

            return $result->exitCode();
        } catch (CommandNotFoundException $commandNotFoundException) {
            $this->output()->error($commandNotFoundException->getMessage());
        } catch (Exception $exception) {
            $message = empty($exception->getMessage()) ? $exception::class : $exception->getMessage();
            $this->output()->error($message);
        }

        return 1;
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
        return $this->boot()->application->handle($this->input(), $this->output());
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
