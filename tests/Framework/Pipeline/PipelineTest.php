<?php

namespace Tests\Framework\Pipeline;

use Myerscode\Acorn\Framework\Exceptions\InvalidPipeException;
use Myerscode\Acorn\Framework\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use Tests\Resources\AfterPipe;
use Tests\Resources\BeforePipe;
use Tests\Resources\PipedObject;

class PipelineTest extends TestCase
{
    public function testLayersAreRunInCorrectOrder()
    {
        $pipeline = new Pipeline();
        $end = $pipeline
            ->pipes(new BeforePipe(1))
            ->pipes(new AfterPipe(4))
            ->pipes(new BeforePipe(3))
            ->pipes(new AfterPipe(2))
            ->flush(new PipedObject, function ($object) {
                $object->passedThrough[] = 'core';

                return $object;
            });

        $this->assertEquals([
            1, 3, 'core', 2, 4,
        ], $end->passedThrough);
    }

    public function testAddingAnOnionAndArrayWorks()
    {
        $pipeline1 = (new Pipeline)->pipes([
            new BeforePipe(1),
            new AfterPipe(4),
        ]);

        $pipeline2 = new Pipeline([
            new BeforePipe(3),
            new AfterPipe(2),
        ]);

        $end = $pipeline1
            ->pipes($pipeline2)
            ->flush(new PipedObject, function ($object) {
                $object->passedThrough[] = 'core';

                return $object;
            });

        $this->assertEquals([
            1, 3, 'core', 2, 4,
        ], $end->passedThrough);
    }

    public function testDefaultHandler()
    {
        $object = new \StdClass;
        $object->runs = [];
        $pipeline = new Pipeline();

        $end = $pipeline->pipes([
            new BeforePipe(1),
            new BeforePipe(2),
            new AfterPipe(3),
            new AfterPipe(4),
        ])->flush($object);

        $this->assertEquals([
            1, 2, 4, 3,
        ], $end->passedThrough);
    }

    public function testOnlyAcceptsPipesWithInterface()
    {
        $object = new \StdClass;
        $object->runs = [];
        $pipeline = new Pipeline();

        $end = $pipeline->pipes([
            new BeforePipe(1),
            function () {
                return 2;
            },
            new AfterPipe(3),
        ])->flush($object);

        $this->assertEquals([
            1, 3,
        ], $end->passedThrough);
    }

    public function testInvalidPipelineException()
    {
        $pipeline = new Pipeline();
        $this->expectException(InvalidPipeException::class);

        $pipeline->pipes('invalid argument');
    }
}
