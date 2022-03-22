<?php

namespace Myerscode\Acorn\Framework\Queue\Jobs;

interface JobInterface
{
    public function work(): void;
}
