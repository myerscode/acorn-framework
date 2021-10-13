<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility;

class BeforeCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandBeforeEvent::class;

    public function __construct(private Output $output)
    {
        //
    }

    public function __invoke(CommandBeforeEvent $event): void
    {
        $message = sprintf('Before running command <info>%s</info>', $event->commandEvent->getCommand()->getName());
        $this->output->verbose($message);
    }
}
