<?php

namespace Tests\Framework\Terminal;

use Myerscode\Acorn\Framework\Terminal\Command;
use Tests\BaseTestCase;

class CommandTest extends BaseTestCase
{
    public function testInstructions(): void
    {
        $command = Command::make('ls -la');

        $this->assertSame('ls -la', $command->instructions());
    }

    public function testMake(): void
    {
        $command = Command::make('ls -la');

        $this->assertInstanceOf(Command::class, $command);
    }

    public function testMakeFromAnotherCommand(): void
    {
        $command = Command::make(Command::make('ls -la'));

        $this->assertInstanceOf(Command::class, $command);
    }
}
