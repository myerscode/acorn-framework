<?php

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Pipeline\PipeInterface;

class BeforePipe implements PipeInterface
{
    private string $id;

    public function __construct(string $id = 'before')
    {
        $this->id = $id;
    }

    public function handle($object, \Closure $next)
    {
        $object->passedThrough[] = $this->id;

        return $next($object);
    }
}
