<?php

namespace Myerscode\Acorn\Framework\Console;

use Exception;
use League\Container\Container;
use Myerscode\Acorn\Foundation\Console\Input\ConfigInput;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    use InteractsWithInput;
    use InteractsWithLogs;
    use InteractsWithOutput;

    protected ConsoleInputInterface $input;

    protected DisplayOutputInterface $output;

    protected Container $container;

    /**
     * The console command name/handle used to call it
     */
    protected ?string $name = null;

    /**
     * The console command description
     */
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

    /**
     * {@inheritDoc}
     */
    public function getAliases(): array
    {
        return array_unique(array_merge([get_called_class()], parent::getAliases()));
    }

    /**
     * What to run when the command is executed
     *
     * @return mixed
     */
    abstract function handle(): void;

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
        $symfonyCommand = $this->getApplication()->find($commandName);

        $parameters = array_merge($parameters, ['command' => $commandName]);
        $arrayInput = new \Myerscode\Acorn\Foundation\Console\Input\ConfigInput($parameters);

        return $symfonyCommand->run($arrayInput, $this->output);
    }

    protected function configureWithSignature(): void
    {
        [$name, $arguments, $options] = (new CommandInterpreter)->parse($this->signature);
        parent::__construct($this->name = $name);
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(ConsoleInputInterface|InputInterface $input, DisplayOutputInterface|OutputInterface $output)
    {
        $this->handle();

        return 0;
    }

    /**
     * Initialize the command.
     *
     * @param  \Myerscode\Acorn\Framework\Console\ConsoleInputInterface  $input
     * @param  \Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface  $output
     */
    protected function initialize(ConsoleInputInterface|InputInterface $input, DisplayOutputInterface|OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
    }

}
