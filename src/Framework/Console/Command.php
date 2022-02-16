<?php

namespace Myerscode\Acorn\Framework\Console;

use Exception;
use League\Container\Container;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{

    use LoggerAwareTrait;

    protected ConsoleInputInterface $input;

    protected ConsoleOutputInterface $output;

    protected Container $container;

    /**
     * The console command name.
     */
    protected ?string $name = null;

    protected string $description = '';

    /**
     * The name and signature of the console command.
     */
    protected string $signature;

    public function __construct()
    {
        if (isset($this->signature)) {
            $this->configureWithSignature();
        } else {
            parent::__construct($this->name);
        }

        $this->setDescription($this->description);
    }

    protected function configureWithSignature()
    {
        [$name, $arguments, $options] = (new CommandInterpreter)->parse($this->signature);
        parent::__construct($this->name = $name);
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * What to run when the command is executed
     *
     * @return mixed
     */
    abstract function handle(): void;

    /**
     * Initialize the command.
     */
    protected function initialize(ConsoleInputInterface|InputInterface $input, ConsoleOutputInterface|OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(ConsoleInputInterface|InputInterface $input, ConsoleOutputInterface|OutputInterface $output)
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
     * @throws Exception
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
     *
     */
    public function hasArgument(string|int $name): bool
    {
        return $this->input->hasArgument($name) && !is_null($this->input->getArgument($name));
    }

    /**
     * Get the value of a command argument.
     *
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
     */
    public function arguments(): array
    {
        return $this->input->getArguments();
    }

    /**
     * Determine if the given option is present.
     *
     *
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
     */
    public function options(): array
    {
        return $this->input->getOptions();
    }
}
