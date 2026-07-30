<?php

namespace Myerscode\Acorn;

use Exception;
use RuntimeException;
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

    private ?Application $application = null;

    private bool $booted = false;

    private readonly Container $container;

    private ?Manager $configManager = null;

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
            $this->output()->error($commandNotFoundException->getMessage());
        } catch (Exception $exception) {
            $message = in_array($exception->getMessage(), ['', '0'], true) ? $exception::class : $exception->getMessage();
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
        $app = $this->boot()->application;
        if (!$app instanceof \Myerscode\Acorn\Application) {
            throw new RuntimeException('Application not booted');
        }

        return $app->handle($this->input(), $this->output());
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
