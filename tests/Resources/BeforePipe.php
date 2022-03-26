<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Pipeline\PipeInterface;
use Closure;

class BeforePipe implements PipeInterface
{
    public function __construct(private readonly string $id = 'before')
    {
    }

    public function handle($object, Closure $next)
    {
        $object->passedThrough[] = $this->id;

        return $next($object);
    }
}
