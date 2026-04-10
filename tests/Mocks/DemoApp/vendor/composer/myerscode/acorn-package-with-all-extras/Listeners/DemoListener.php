<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DemoEvent;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;

class DemoListener extends Listener
{
    #[\Override]
    protected string|array $listensFor = DemoEvent::class;

    public function __construct(protected DisplayOutput $displayOutput)
    {
        //
    }

    #[\Override]
    public function handle(EventInterface $event): void
    {
        if (!$event instanceof DemoEvent) {
            return;
        }

        $this->displayOutput->info(DemoEvent::class.' event emitted at '.$event->timestamp);
    }
}
