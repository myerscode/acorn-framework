<?php

namespace Myerscode\Acorn;

use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Events\Bus;
use Myerscode\Acorn\Framework\Exception\AppConfigException;
use Myerscode\Acorn\Framework\Helpers\Files\FileService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Application extends SymfonyApplication
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @var Container
     */
    private Container $container;
    /**
     * @var Bus
     */
    private Bus $event;

    public function __construct(Container $container, Bus $event)
    {
        parent::__construct('Acorn');

        $this->event = $event;

        // allow command exceptions to bubble up and be handled by the kernal
        $this->setCatchExceptions(false);

        $this->container = $container;

        $this->createLogger();

        $this->bindCommandEvents();
    }

    protected function bindCommandEvents()
    {
        $dispatcher = new EventDispatcher;

        $dispatcher->addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {
            $command = $event->getCommand();
            $event->getOutput()->writeln(sprintf('Before running command <info>%s</info>', $command->getName()));
            $this->event->emit(CommandBeforeEvent::class, $event);
        });

        $dispatcher->addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {
            $command = $event->getCommand();
            $event->getOutput()->writeln(sprintf('After running command <info>%s</info>', $command->getName()));
            $this->event->emit(CommandAfterEvent::class, $event);
        });

        $dispatcher->addListener(ConsoleEvents::ERROR, function (ConsoleErrorEvent $event) {
            $command = $event->getCommand();
            $event->getOutput()->writeln(sprintf('Error running command <info>%s</info>', $command->getName()));
            $this->event->emit(CommandErrorEvent::class, $event);
        });

        $this->setDispatcher($dispatcher);
    }

    public function add(SymfonyCommand $command)
    {

        if ($command instanceof Command || $command instanceof LoggerAwareInterface) {
            $command->setLogger($this->logger());
        }

        return parent::add($command);
    }

    /**
     * Get the logger instance.
     *
     * @return LoggerInterface
     */
    public function logger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * Create logger instance.
     */
    protected function createLogger()
    {
        // TODO make this configurable

        $this->logger = new NullLogger();

        if (!$this->logger instanceof LoggerInterface) {
            throw new AppConfigException('Logger must be implement the "'.LoggerInterface::class.'" interface.');
        }
    }
}
