<?php

namespace App\Listeners;

use App\Events\DemoEvent;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Events\Listener;

class DemoListener extends Listener
{
    protected string|array $listensFor = DemoEvent::class;

    public function __construct(protected DisplayOutput $output)
    {
        //
    }

    public function __invoke(DemoEvent $event): void
    {
        $this->output->info(DemoEvent::class.' event emitted at '.$event->timestamp);
    }
}
