<?php

namespace Myerscode\Acorn\Framework\Pipeline;

use Closure;

interface PipeInterface
{
    public function handle($object, Closure $next);
}
