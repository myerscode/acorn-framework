<?php

namespace Tests\Framework\Pipeline;

use Myerscode\Acorn\Framework\Exceptions\InvalidPipeException;
use Myerscode\Acorn\Framework\Pipeline\Pipeline;
use PHPUnit\Framework\TestCase;
use StdClass;
use Tests\Resources\AfterPipe;
use Tests\Resources\BeforePipe;
use Tests\Resources\PipedObject;

class PipelineTest extends TestCase
{
    public function testAddingAnPipelineAndArrayWorks(): void
    {
        $pipeline1 = (new Pipeline)->pipes([
            new BeforePipe(1),
            new AfterPipe(4),
        ]);

        $pipeline = new Pipeline([
            new BeforePipe(3),
            new AfterPipe(2),
        ]);

        $end = $pipeline1
            ->pipes($pipeline)
            ->flush(new PipedObject, static function ($object) {
                $object->passedThrough[] = 'core';

                return $object;
            });

        $this->assertSame([
            '1',
            '3',
            'core',
            '2',
            '4',
        ], $end->passedThrough);
    }

    public function testCanAddASinglePipe(): void
    {
        $stdClass = new StdClass();
        $pipeline = (new Pipeline)->pipes(new BeforePipe)->flush($stdClass);

        $this->assertSame(['before'], $pipeline->passedThrough);

        $stdClass = new StdClass();
        $pipeline = (new Pipeline(AfterPipe::class))->flush($stdClass);

        $this->assertSame(['after'], $pipeline->passedThrough);
    }

    public function testDefaultHandler(): void
    {
        $stdClass = new StdClass();

        $stdClass->runs = [];
        $pipeline = new Pipeline();

        $end = $pipeline->pipes([
            new BeforePipe(1),
            new BeforePipe(2),
            new AfterPipe(3),
            new AfterPipe(4),
        ])->flush($stdClass);

        $this->assertSame([
            '1',
            '2',
            '4',
            '3',
        ], $end->passedThrough);
    }

    public function testInvalidPipelineException(): void
    {
        $pipeline = new Pipeline();
        $this->expectException(InvalidPipeException::class);

        $pipeline->pipes('invalid argument');
    }

    public function testLayersAreRunInCorrectOrder(): void
    {
        $pipeline = new Pipeline();
        $end = $pipeline
            ->pipes(new BeforePipe(1))
            ->pipes(new AfterPipe(4))
            ->pipes(new BeforePipe(3))
            ->pipes(new AfterPipe(2))
            ->flush(new PipedObject, static function ($object) {
                $object->passedThrough[] = 'core';

                return $object;
            });

        $this->assertSame([
            '1',
            '3',
            'core',
            '2',
            '4',
        ], $end->passedThrough);
    }

    public function testOnlyAcceptsPipesWithInterface(): void
    {
        $stdClass = new StdClass();

        $stdClass->runs = [];

        $pipeline = new Pipeline();

        $end = $pipeline->pipes([
            new BeforePipe(1),
            static fn(): int => 2,
            new AfterPipe(3),
        ])->flush($stdClass);

        $this->assertSame([
            '1',
            '3',
        ], $end->passedThrough);
    }

    public function testPipesCanBeDefinedByClass(): void
    {
        $stdClass = new StdClass();

        $pipeline = (new Pipeline)->pipes([
            BeforePipe::class,
            AfterPipe::class,
        ])->flush($stdClass);

        $this->assertSame(['before', 'after'], $pipeline->passedThrough);
    }
}
