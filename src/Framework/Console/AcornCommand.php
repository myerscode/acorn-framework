<?php

namespace Myerscode\Acorn\Framework\Console;

use League\Container\Container as DependencyManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

abstract class AcornCommand extends Command implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var DependencyManager
     */
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
     * Return logger instance.
     *
     * @return LoggerInterface
     */
    protected function logger()
    {
        return $this->logger;
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
    protected function call($commandName, $parameters = [])
    {
        $command = $this->getApplication()->find($commandName);
        $parameters = array_merge($parameters, ['command' => $commandName]);
        $arrayInput = new ArrayInput($parameters);

        return $command->run($arrayInput, $this->output);
    }

    /**
     * Proxy method of OutputInterface::writeln.
     *
     * @param  string|iterable  $messages
     * @param  int  $options
     *
     * @return mixed
     * @see \Symfony\Component\Console\Output\OutputInterface::writeln
     *
     */
    private function line($messages, $options = 0)
    {
        return $this->output->writeln($messages, $options);
    }

    /**
     * Writeln with info color style.
     *
     * @param  string  $message
     * @param  int  $options
     */
    protected function info(string $message, $options = 0)
    {
        $this->line("<info>{$message}</info>", $options);
    }

    /**
     * Writeln with comment color style.
     *
     * @param  string  $message
     * @param  int  $options
     */
    protected function comment(string $message, $options = 0)
    {
        $this->line("<comment>{$message}</comment>", $options);
    }

    /**
     * Writeln with question color style.
     *
     * @param  string  $message
     * @param  int  $options
     */
    protected function question(string $message, $options = 0)
    {
        $this->line("<question>{$message}</question>", $options);
    }

    /**
     * Writeln with error color style.
     *
     * @param  string  $message
     * @param  int  $options
     */
    protected function error(string $message, $options = 0)
    {
        $this->line("<error>{$message}</error>", $options);
    }

    /**
     * @param  array  $header
     * @param  array  $rows
     */
    protected function table(array $header = [], array $rows = [])
    {
        $table = new Table($this->output);
        $table->setHeaders($header)->setRows($rows);

        return $table;
    }

    /**
     * Confirm the question.
     *
     * @param  string  $question  Question content
     * @param  bool  $default  Default return value
     *
     * @return bool
     */
    protected function confirm(string $question, bool $default = true)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion($question, $default);

        return $helper->ask($this->input, $this->output, $question);
    }
}
