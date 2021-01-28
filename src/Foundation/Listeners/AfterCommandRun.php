<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility as Text;

class AfterCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandAfterEvent::class;

    private Text $utility;

    private Output $output;

    public function __construct(Text $utility, Output $output)
    {
        $this->utility = $utility;
        $this->output = $output;
    }

    public function __invoke(CommandAfterEvent $event): void
    {
        $this->output->verbose(sprintf('After running command <info>%s</info>', $event->commandEvent->getCommand()->getName()));
    }
}
