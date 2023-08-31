<?php

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Exception\ProcessFailedException;
use Myerscode\Acorn\Framework\Terminal\FailedResponse;
use Tests\BaseTestCase;
use Tests\Support\InteractsWithProcess;

class FailedResponseTest extends BaseTestCase
{
    use InteractsWithProcess;

    public function testSetError()
    {
        $process = $this->mockedFailedProcess(function ($mock) {
            $mock->shouldReceive('getCommandLine')->andReturn('ls -la');
        });

        $response = new FailedResponse($process, 1);
        $response->setError(new ProcessFailedException($process));

        $this->assertInstanceOf(ProcessFailedException::class, $response->error());

        $this->assertEquals($process, $response->process());
    }
}
