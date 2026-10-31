<?php

declare(strict_types=1);

namespace Tests\Framework\Console;

use Myerscode\Acorn\Framework\Console\InteractsWithInput;
use Myerscode\Acorn\Framework\Console\InteractsWithOutput;
use Tests\BaseTestCase;

final class InteractsWithInputTest extends BaseTestCase
{
    public function testArgument(): void
    {
        [$trait] = $this->createTrait();

        $this->assertSame('Corgi', $trait->argument('breed'));
    }

    public function testArguments(): void
    {
        [$trait] = $this->createTrait();

        $this->assertSame(['breed' => 'Corgi'], $trait->arguments());
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

        // option() is for value options; a boolean switch is not a string
        // value, so it yields the default rather than the raw bool.
        $this->assertNull($trait->option('fluff'));

        $this->assertSame('ball', $trait->option('toy'));

        $this->assertNull($trait->option('dog'));

        $this->assertSame('fallback', $trait->option('dog', 'fallback'));
    }

    public function testFlagReportsSwitchTruthiness(): void
    {
        [$trait] = $this->createTrait(
            ['breed' => 'Corgi', '--fluff' => true],
            '{breed} {--fluff} {--trim} {--toy=ball}',
        );

        // Passed switch is true; a defined-but-absent switch is false;
        // an undefined option is false (never a null-based truthy slip).
        $this->assertTrue($trait->flag('fluff'));

        $this->assertFalse($trait->flag('trim'));

        $this->assertFalse($trait->flag('undefined'));
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
        $trait = new class () {
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

        $consoleInput = $this->createInput($userInput, $signature);

        $output = $this->createStreamOutput($consoleInput);

        $this->setObjectProperties($trait, ['input' => $consoleInput, 'output' => $output]);

        return [$trait, $consoleInput, $output];
    }
}
