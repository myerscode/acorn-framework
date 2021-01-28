<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandBeforeEvent;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility as Text;

class BeforeCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandBeforeEvent::class;

    private Text $utility;

    private Output $output;

    public function __construct(Text $utility, Output $output)
    {
        $this->utility = $utility;
        $this->output = $output;
    }

    public function __invoke(CommandBeforeEvent $event): void
    {
        $this->output->verbose(sprintf('Before running command <info>%s</info>', $event->commandEvent->getCommand()->getName()));
    }
}
