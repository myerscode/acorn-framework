<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Queue\Jobs;

interface JobInterface
{
    public function work(): void;
}
