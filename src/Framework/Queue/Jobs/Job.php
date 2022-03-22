<?php

namespace Myerscode\Acorn\Framework\Queue\Jobs;

abstract class Job
{
    public function __construct(protected $event, protected $handler)
    {
        //
    }
}
