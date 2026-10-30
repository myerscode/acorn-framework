<?php

declare(strict_types=1);

namespace Tests\Framework\Events;

use stdClass;
use Myerscode\Acorn\Framework\Events\CallableListener;
use Myerscode\Acorn\Framework\Events\Exception\InvalidCallableConstructException;
use Tests\BaseTestCase;

final class CallableListenerTest extends BaseTestCase
{
    public function testCallableListenerAcceptsCallables(): void
    {
        $callable = new class () {
            public function __invoke(): void
            {

            }
        };

        $callableListener = new CallableListener($callable);

        $this->assertEquals($callableListener->getCallable(), $callable);
    }

    public function testCallableListenerAcceptsClosures(): void
    {
        $closure = static function (): void {
        };
        $callableListener = new CallableListener($closure);

        $this->assertEquals($callableListener->getCallable(), $closure);
    }

    public function testCallableListenerThrowsErrorIfNotClosureOrCallable(): void
    {
        $this->expectException(InvalidCallableConstructException::class);
        new CallableListener(new stdClass());
    }
}
