<?php

namespace Tests\Framework\Events;

use stdClass;
use Myerscode\Acorn\Framework\Events\CallableListener;
use Myerscode\Acorn\Framework\Events\Exception\InvalidCallableConstructException;
use Tests\BaseTestCase;

class CallableListenerTest extends BaseTestCase
{

    public function testCallableListenerAcceptsClosures(): void
    {
        $closure = static function () : void {
        };
        $callableListener = new CallableListener($closure);

        $this->assertEquals($callableListener->getCallable(), $closure);
    }

    public function testCallableListenerAcceptsCallables(): void
    {
        $callable = new class {
            public function __invoke()
            {

            }
        };

        $callableListener = new CallableListener($callable);

        $this->assertEquals($callableListener->getCallable(), $callable);
    }

    public function testCallableListenerThrowsErrorIfNotClosureOrCallable(): void
    {
        $this->expectException(InvalidCallableConstructException::class);
        new CallableListener(new stdClass());
    }
}
