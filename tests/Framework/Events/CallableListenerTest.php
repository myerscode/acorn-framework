<?php

namespace Tests\Framework\Events;

use Myerscode\Acorn\Framework\Events\CallableListener;
use Myerscode\Acorn\Framework\Events\Exception\InvalidCallableConstructException;
use PHPUnit\Framework\TestCase;

class CallableListenerTest extends TestCase
{

    public function testCallableListenerAcceptsClosures()
    {
        $closure = function () {

        };
        $listener = new CallableListener($closure);

        $this->assertEquals($listener->getCallable(), $closure);
    }

    public function testCallableListenerAcceptsCallables()
    {
        $callable = new class {
            public function __invoke()
            {

            }
        };

        $listener = new CallableListener($callable);

        $this->assertEquals($listener->getCallable(), $callable);
    }

    public function testCallableListenerThrowsErrorIfNotClosureOrCallable()
    {
        $this->expectException(InvalidCallableConstructException::class);
        $listener = new CallableListener(new \stdClass());
    }
}
