<?php

namespace Tests\Framework\Pipeline;

use Myerscode\Acorn\Framework\Pipeline\LineManager;
use PHPUnit\Framework\TestCase;
use Tests\Resources\AfterPipe;
use Tests\Resources\BeforePipe;
use Tests\Resources\PipedObject;

class LineManagerTest extends TestCase
{
    public function testRegisteringPipelineWithClasses()
    {
        $manager = new LineManager();

        $manager->setPipeline('test', [
            BeforePipe::class,
            AfterPipe::class,
        ]);

        $response = $manager->send(new PipedObject)->through('test');

        $this->assertEquals(['before', 'after'], $response->passedThrough);
    }

    public function testRegisteringPipelineWithObjects()
    {
        $manager = new LineManager();

        $manager->setPipeline('test', [
            new BeforePipe('123'),
            new AfterPipe('456'),
        ]);

        $response = $manager->send(new PipedObject)->through('test');

        $this->assertEquals(['123', '456'], $response->passedThrough);
    }
}
