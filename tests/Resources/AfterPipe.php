<?php

namespace Tests\Resources;

use Closure;
use Myerscode\Acorn\Framework\Pipeline\PipeInterface;

class AfterPipe implements PipeInterface
{
    public function __construct(private readonly string $id = 'after')
    {
    }

    public function handle($object, Closure $next)
    {
        $response = $next($object);

        $object->passedThrough[] = $this->id;

        return $response;
    }

}
