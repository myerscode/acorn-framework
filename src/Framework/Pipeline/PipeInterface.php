<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Pipeline;

use Closure;

interface PipeInterface
{
    public function handle(mixed $object, Closure $next): mixed;
}
