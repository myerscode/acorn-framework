<?php

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\Result;
use Tests\BaseTestCase;
use InvalidArgumentException;

class ResultTest extends BaseTestCase
{
    public function testResultKnowsCommandWasSuccessful(): void
    {
        $result = new Result(0);
        $this->assertTrue($result->wasSuccessful());
        $this->assertFalse($result->failed());
    }

    public function testResultKnowsCommandFailed(): void
    {
        $result = new Result(random_int(1, 10000));
        $this->assertTrue($result->failed());
        $this->assertFalse($result->wasSuccessful());
    }

    public function testResultKnowsException(): void
    {
        $result = new Result(1, new InvalidArgumentException());
        $this->assertInstanceOf(InvalidArgumentException::class, $result->error());
    }

    public function testResultKnowsExitCode(): void
    {
        $result = new Result(1, new InvalidArgumentException());
        $this->assertSame(1, $result->exitCode());
    }
}
