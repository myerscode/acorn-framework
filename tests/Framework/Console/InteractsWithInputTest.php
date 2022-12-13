<?php

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\InteractsWithInput;
use Myerscode\Acorn\Framework\Console\InteractsWithOutput;
use Tests\BaseTestCase;

class InteractsWithInputTest extends BaseTestCase
{
    public function testArgument(): void
    {
        [$trait] = $this->createTrait();

        $this->assertEquals('Corgi', $trait->argument('breed'));
    }

    public function testArguments(): void
    {
        [$trait] = $this->createTrait();

        $this->assertEquals(['breed' => 'Corgi'], $trait->arguments());
    }

    public function testHasArgument(): void
    {
        [$trait] = $this->createTrait();

        $this->assertTrue($trait->hasArgument('breed'));

        $this->assertFalse($trait->hasArgument('corgi'));
    }

    public function testHasOption(): void
    {
        [$trait] = $this->createTrait();

        $this->assertTrue($trait->hasOption('fluff'));

        $this->assertTrue($trait->hasOption('toy'));

        $this->assertFalse($trait->hasOption('corgi'));
    }

    public function testOption(): void
    {
        [$trait] = $this->createTrait();

        $this->assertEquals(true, $trait->option('fluff'));

        $this->assertEquals('ball', $trait->option('toy'));

        $this->assertEquals(false, $trait->option('dog'));
    }

    public function testOptions(): void
    {
        [$trait] = $this->createTrait();

        $this->assertEquals([
            'fluff' => true,
            'toy' => 'ball',
        ], $trait->options());
    }

    public function testParameters(): void
    {
        [$trait] = $this->createTrait();

        $this->assertEquals([
            'breed' => 'Corgi',
            '--fluff' => true,
        ], $trait->parameters());
    }

    private function createTrait($userInput = [], string $signature = ''): array
    {
        $trait = new class {
            use InteractsWithInput;
            use InteractsWithOutput;
        };

        if (empty($userInput)) {
            $userInput = [
                'breed' => 'Corgi',
                '--fluff' => true,
            ];
        }

        if (!strlen($signature) > 0) {
            $signature = '{breed} {--fluff} {--toy=ball}';
        }

        $input = $this->createInput($userInput, $signature);

        $output = $this->createStreamOutput($input);

        $this->setObjectProperties($trait, ['input' => $input, 'output' => $output]);

        return [$trait, $input, $output];
    }
}
