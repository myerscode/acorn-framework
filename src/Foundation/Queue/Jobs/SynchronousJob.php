<?php

namespace Myerscode\Acorn\Foundation\Queue\Jobs;

use Myerscode\Acorn\Framework\Queue\Jobs\Job;
use Myerscode\Acorn\Framework\Queue\Jobs\JobInterface;

class SynchronousJob extends Job implements JobInterface
{
    public function work(): void
    {
        if (method_exists($this->handler, 'handle')) {
            $this->handler->handle($this->event);
        }
    }
}
