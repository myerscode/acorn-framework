<?php

declare(strict_types=1);

namespace Tests\Foundation\Console\Input;

use PHPUnit\Framework\Attributes\BackupGlobals;
use Myerscode\Acorn\Foundation\Console\Input\Input;
use PHPUnit\Framework\TestCase;

final class InputTest extends TestCase
{
    #[BackupGlobals(true)]
    public function testParametersParsedFromArgs(): void
    {
        $_SERVER['argv'] = ['acorn', 'list', '--all'];
        $_SERVER['argc'] = 3;

        $input = new Input();

        $this->assertSame($_SERVER['argv'], $input->parameters());
    }

    #[BackupGlobals(true)]
    public function testParametersParsedFromArray(): void
    {
        $manualInput = ['acorn', 'list', '--commands'];

        $input = new Input($manualInput);

        $this->assertSame($manualInput, $input->parameters());
    }
}
