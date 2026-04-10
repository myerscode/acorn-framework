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
    #[\Override]
    protected string|array $listensFor = CommandBeforeEvent::class;

    public function __construct(private readonly DisplayOutput $displayOutput)
    {
        //
    }

    public function __invoke(CommandBeforeEvent $commandBeforeEvent): void
    {
        $message = sprintf('Before running command <info>%s</info>', $commandBeforeEvent->consoleCommandEvent->getCommand()->getName());
        $this->displayOutput->verbose($message);
    }
}
