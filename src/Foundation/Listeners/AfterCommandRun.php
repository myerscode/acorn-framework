<?php

namespace Myerscode\Acorn\Foundation\Listeners;

use Myerscode\Acorn\Foundation\Events\CommandAfterEvent;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Events\Listener;
use Myerscode\Utilities\Strings\Utility;

class AfterCommandRun extends Listener
{
    /**
     * @var string[]|string
     */
    protected $listensFor = CommandAfterEvent::class;

    public function __construct(private readonly Output $output)
    {
        //
    }

    public function __invoke(CommandAfterEvent $commandAfterEvent): void
    {
        $message = sprintf('After running command <info>%s</info>', $commandAfterEvent->consoleTerminateEvent->getCommand()->getName());
        $this->output->verbose($message);
    }
}
