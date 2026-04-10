<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DemoEvent;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Events\Listener;

class DemoListener extends Listener
{
    #[\Override]
    protected string|array $listensFor = DemoEvent::class;

    public function __construct(protected DisplayOutput $displayOutput)
    {
        //
    }

    public function __invoke(DemoEvent $demoEvent): void
    {
        $this->displayOutput->info(DemoEvent::class.' event emitted at '.$demoEvent->timestamp);
    }
}
