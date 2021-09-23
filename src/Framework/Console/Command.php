<?php

namespace Myerscode\Acorn\Framework\Console;

use League\Container\Container as DependencyManager;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{

    use LoggerAwareTrait;

    protected InputInterface $input;

    protected OutputInterface $output;

    protected DependencyManager $container;

    /**
     * BaseCommand constructor.
     *
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    public function setContainer(DependencyManager $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function getContainer(): DependencyManager
    {
        return $this->container;
    }

    /**
     * What to run when the command is executed
     *
     * @return mixed
     */
    abstract function handle(): void;

    /**
     * Initialize the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->handle();

        return 0;
    }

    /**
     * Call other command.
     *
     * @param  string  $commandName  Command name
     * @param  array  $parameters  Command parameters
     *
     * @return int Exit code
     *
     * @throws \Exception
     */
    protected function call(string $commandName, array $parameters = []): int
    {
        $command = $this->getApplication()->find($commandName);
        $parameters = array_merge($parameters, ['command' => $commandName]);
        $arrayInput = new ArrayInput($parameters);

        return $command->run($arrayInput, $this->output);
    }

    /**
     * Determine if the given argument is present.
     *
     * @param  string|int  $name
     *
     * @return bool
     */
    public function hasArgument($name): bool
    {
        return $this->input->hasArgument($name) && !is_null($this->input->getArgument($name));
    }

    /**
     * Get the value of a command argument.
     *
     * @param  string  $key
     * @param  string|null  $default
     *
     * @return string|array|null
     */
    public function argument(string $key, string $default = null)
    {
        if ($this->hasArgument($key)) {
            return $this->input->getArgument($key);
        }

        return $default;
    }

    /**
     * Get all of the arguments passed to the command.
     *
     * @return array
     */
    public function arguments(): array
    {
        return $this->input->getArguments();
    }

    /**
     * Determine if the given option is present.
     *
     * @param  string  $name
     *
     * @return bool
     */
    public function hasOption(string $name): bool
    {
        return $this->input->hasOption($name) && !is_null($this->input->getOption($name));
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @param  string|null  $default
     *
     * @return string|null
     */
    public function option(string $key, string $default = null): ?string
    {
        if ($this->hasOption($key)) {
            return $this->input->getOption($key);
        }

        return $default;
    }

    /**
     * Get all of the options passed to the command.
     *
     * @return array
     */
    public function options(): array
    {
        return $this->input->getOptions();
    }
}
