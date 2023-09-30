<?php

namespace Tests\Framework\Pipeline;

use Myerscode\Acorn\Framework\Pipeline\LineManager;
use PHPUnit\Framework\TestCase;
use Tests\Resources\AfterPipe;
use Tests\Resources\BeforePipe;
use Tests\Resources\PipedObject;

class LineManagerTest extends TestCase
{
    public function testRegisteringPipelineWithClasses(): void
    {
        $lineManager = new LineManager();

        $lineManager->setPipeline('test', [
            BeforePipe::class,
            AfterPipe::class,
        ]);

        $response = $lineManager->send(new PipedObject)->through('test');

        $this->assertSame(['before', 'after'], $response->passedThrough);
    }

    public function testRegisteringPipelineWithObjects(): void
    {
        $lineManager = new LineManager();

        $lineManager->setPipeline('test', [
            new BeforePipe('123'),
            new AfterPipe('456'),
        ]);

        $response = $lineManager->send(new PipedObject)->through('test');

        $this->assertSame(['123', '456'], $response->passedThrough);
    }

    public function testCanBePassedThroughMultiplePipelines(): void
    {
        $lineManager = new LineManager();

        $lineManager->setPipeline('first', [
            new BeforePipe('123'),
            new AfterPipe('456'),
        ]);

        $lineManager->setPipeline('second', [
            new BeforePipe('abc'),
            new AfterPipe('xyz'),
        ]);

        $response = $lineManager->send(new PipedObject)->through(['first', 'second']);

        $this->assertSame(['123', '456', 'abc', 'xyz'], $response->passedThrough);
    }
}
