<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Framework\Events\Listener;

class BeforeCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected string|array $listensFor = CommandBeforeEvent::class;

    public function __construct(private readonly DisplayOutput $output)
    {
        //
    }

    public function __invoke(CommandBeforeEvent $event): void
    {
        $message = sprintf('Before running command <info>%s</info>', $event->consoleCommandEvent->getCommand()->getName());
        $this->output->verbose($message);
    }
}
