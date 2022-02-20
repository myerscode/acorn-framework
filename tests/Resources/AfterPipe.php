<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Pipeline\PipeInterface;

class AfterPipe implements PipeInterface
{
    private string $id;

    public function __construct(string $id = 'after')
    {
        $this->id = $id;
    }

    public function handle($object, \Closure $next)
    {
        $response = $next($object);

        $object->passedThrough[] = $this->id;

        return $response;
    }

}
