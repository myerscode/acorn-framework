<?php

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\FailedResponse;
use Tests\BaseTestCase;
use Tests\Support\InteractsWithProcess;

class FailedResponseTest extends BaseTestCase
{
    use InteractsWithProcess;

    public function testSetError(): void
    {
        $process = $this->mockedFailedProcess(static function ($mock) : void {
            $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
        });

        $failedResponse = new FailedResponse($process, 1);
        $failedResponse->setError(new ProcessFailedException($process));

        $this->assertInstanceOf(ProcessFailedException::class, $failedResponse->error());

        $this->assertEquals($process, $failedResponse->process());
    }
}
