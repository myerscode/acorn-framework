<?php

namespace Tests\Foundation\Console\Input;

use PHPUnit\Framework\Attributes\BackupGlobals;
use Myerscode\Acorn\Foundation\Console\Input\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    #[BackupGlobals(true)]
    public function testParametersParsedFromArgs(): void
    {
        $_SERVER['argv'] = ['acorn', 'list', '--all'];
        $_SERVER['argc'] = 3;

        $input = new Input();

        $this->assertEquals($_SERVER['argv'], $input->parameters());
    }

    #[BackupGlobals(true)]
    public function testParametersParsedFromArray(): void
    {
        $manualInput = ['acorn', 'list', '--commands'];

        $input = new Input($manualInput);

        $this->assertSame($manualInput, $input->parameters());
    }
}
