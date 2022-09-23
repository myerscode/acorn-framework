<?php

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\Result;
use Tests\BaseTestCase;
use InvalidArgumentException;

class ResultTest extends BaseTestCase
{
    public function testResultKnowsCommandWasSuccessful()
    {
        $result = new Result(0);
        $this->assertTrue($result->wasSuccessful());
        $this->assertFalse($result->failed());
    }

    public function testResultKnowsCommandFailed()
    {
        $result = new Result(rand(1, 10000));
        $this->assertTrue($result->failed());
        $this->assertFalse($result->wasSuccessful());
    }

    public function testResultKnowsException()
    {
        $result = new Result(1, new InvalidArgumentException());
        $this->assertInstanceOf(InvalidArgumentException::class, $result->error());
    }

    public function testResultKnowsExitCode()
    {
        $result = new Result(1, new InvalidArgumentException());
        $this->assertEquals(1, $result->exitCode());
    }
}
