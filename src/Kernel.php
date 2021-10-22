<?php

namespace Myerscode\Acorn;

use Exception;
use Myerscode\Acorn\Framework\Console\ConsoleInputInterface;
use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Myerscode\Acorn\Framework\Events\Dispatcher;
use Myerscode\Config\Config;
use Myerscode\Utilities\Files\Exceptions\NotADirectoryException;
use Myerscode\Utilities\Files\Utility as FileService;
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
        $config = new Config();

        $configLocations = [
            __DIR__.'/Config',
            getcwd().'/Config',
        ];

        $config->loadData([
            'base' => $this->basePath,
            'src' => __DIR__,
            'cwd' => getcwd(),
            'configLocations' => $configLocations
        ]);


        foreach ($configLocations as $configLocation) {
            try {
                $configFiles = array_map(fn($file) => $file->getRealPath(), FileService::make($configLocation)->files());
                $config->loadFilesWithNamespace($configFiles);
            } catch (NotADirectoryException) {
            }
        }

        $this->container->add('config', $config);
    }

    public function input(): ConsoleInputInterface
    {
        return $this->container->manager()->get('input');
    }

    public function output(): ConsoleOutputInterface
    {
        return $this->container->manager()->get('output');
    }

    /**
     * Runs the core application
     */
    public function run(): int
    {
        try {
            $result = $this->application->run($this->input(), $this->output());

            // TODO if result failed but has no error do something
            if ($result->failed() !== 0) {
                throw $result->error();
            }

            return $result->exitCode();

        } catch (CommandNotFoundException $commandNotFoundException) {
            $this->output()->info($commandNotFoundException->getMessage());
        } catch (Exception $exception) {
            $this->output()->error($exception->getMessage());
        }

        return 1;
    }

    public function application(): Application
    {
        return $this->application;
    }

    public function container(): Container
    {
        return $this->container;
    }

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
