<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility as Text;

class CommandError extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandErrorEvent::class;

    private Text $utility;

    private Output $output;

    public function __construct(Text $utility, Output $output)
    {
        $this->utility = $utility;
        $this->output = $output;
    }

    public function __invoke(CommandErrorEvent $event): void
    {
        $this->output->verbose(sprintf('Error running command <info>%s</info>', $event->commandEvent->getCommand()->getName()));
    }
}
