<?php

namespace Myerscode\Acorn\Framework\Console;

use Override;
use League\Container\Container;
use RuntimeException;
use Myerscode\Acorn\Foundation\Console\Input\ConfigInput;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Myerscode\Acorn\Framework\Console\Exception\SubCommandFailedException;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends SymfonyCommand
{
    use InteractsWithInput;
    use InteractsWithLogs;
    use InteractsWithOutput;

    protected Container $container;

    /**
     * The console command description
     */
    protected string $description = '';

    /**
     * Should the command be hidden
     */
    protected bool $hide = false;

    protected ConsoleInputInterface $input;

    /**
     * The console command name/handle used to call it
     */
    protected ?string $name = null;

    protected DisplayOutputInterface $output;

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
        $this->setHidden($this->hide);
    }

    /**
     * What to run when the command is executed.
     *
     * Return an integer exit code to signal success (0) or failure (non-zero).
     */
    abstract public function handle(): int;

    /**
     * {@inheritDoc}
     *
     * @return string[]
     */
    #[Override]
    public function getAliases(): array
    {
        return array_unique(array_merge([static::class], parent::getAliases()));
    }

    /**
     * Call other command.
     *
     * @param  string  $commandName  Command name
     * @param  array<string, mixed>  $parameters  Command parameters
     *
     * @return int Exit code
     *
     * @throws SubCommandFailedException
     */
    protected function call(string $commandName, array $parameters = []): int
    {
        try {
            $application = $this->getApplication();
            if (!$application instanceof \Symfony\Component\Console\Application) {
                throw new SubCommandFailedException(new RuntimeException('No application available'), 0);
            }

            $symfonyCommand = $application->find($commandName);

            $parameters = array_merge($parameters, ['command' => $commandName]);
            $configInput = new ConfigInput($parameters);

            return $symfonyCommand->run($configInput, $this->output);
        } catch (ExceptionInterface $exception) {
            throw new SubCommandFailedException($exception, $exception->getCode(), $exception);
        }
    }

    protected function configureWithSignature(): void
    {
        [$name, $arguments, $options] = new CommandInterpreter()->parse($this->signature);
        parent::__construct($this->name = $name);
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(ConsoleInputInterface|InputInterface $input, DisplayOutputInterface|OutputInterface $output): int
    {
        return $this->handle();
    }

    /**
     * Initialize the command.
     *
     * @param ConsoleInputInterface $input
     * @param DisplayOutputInterface $output
     */
    protected function initialize(ConsoleInputInterface|InputInterface $input, DisplayOutputInterface|OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;
    }

}
