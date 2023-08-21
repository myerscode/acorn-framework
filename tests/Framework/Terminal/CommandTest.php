<?php

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Command;
use Tests\BaseTestCase;

class CommandTest extends BaseTestCase
{
    public function testInstructions()
    {
        $command = Command::make('ls -la');

        $this->assertEquals('ls -la', $command->instructions());
    }

    public function testMake()
    {
        $command = Command::make('ls -la');

        $this->assertInstanceOf(Command::class, $command);
    }

    public function testMakeFromAnotherCommand()
    {
        $command = Command::make(Command::make('ls -la'));

        $this->assertInstanceOf(Command::class, $command);
    }
}
