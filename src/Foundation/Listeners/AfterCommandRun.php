<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Framework\Events\Listener;

class AfterCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = CommandAfterEvent::class;

    public function __construct(private readonly DisplayOutput $output)
    {
        //
    }

    public function __invoke(CommandAfterEvent $commandAfterEvent): void
    {
        $message = sprintf('After running command <info>%s</info>', $commandAfterEvent->consoleTerminateEvent->getCommand()->getName());
        $this->output->verbose($message);
    }
}
