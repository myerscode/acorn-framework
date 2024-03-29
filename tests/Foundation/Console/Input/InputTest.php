<?php

namespace Tests\Foundation\Console\Input;

use Myerscode\Acorn\Foundation\Console\Input\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    /**
     * @backupGlobals enabled
     */
    public function testParametersParsedFromArgs(): void
    {
        $_SERVER['argv'] = ['acorn', 'list', '--all'];
        $_SERVER['argc'] = 3;

        $input = new Input();

        $this->assertEquals($_SERVER['argv'], $input->parameters());
    }

    /**
     * @backupGlobals enabled
     */
    public function testParametersParsedFromArray(): void
    {
        $manualInput = ['acorn', 'list', '--commands'];

        $input = new Input($manualInput);

        $this->assertSame($manualInput, $input->parameters());
    }
}
