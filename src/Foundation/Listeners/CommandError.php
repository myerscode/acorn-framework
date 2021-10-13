<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandErrorEvent;
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility;

class CommandError extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandErrorEvent::class;

    public function __construct(private Output $output)
    {
        //
    }

    public function __invoke(CommandErrorEvent $event): void
    {
        if ($command = $event->commandEvent->getCommand() instanceof Command) {
            $commaName = $event->commandEvent->getCommand()->getName();
        }

        $message = sprintf('Error running command <info>%s</info>', $commaName ?? 'UNKNOWN');

        $this->output->verbose($message);
    }
}
