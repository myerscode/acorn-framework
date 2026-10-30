<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Queue\Jobs;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\ListenerInterface;

abstract class Job
{
    public function __construct(protected EventInterface $event, protected ListenerInterface $handler)
    {
        //
    }
}
