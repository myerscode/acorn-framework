<?php

namespace Tests\Mocks\DemoApp\App\Commands;

class NotAnAcornCommand
{
    protected string $signature = 'simple {name : Say hello}';

    protected string $description = 'A simple text command with text output.';

    public function handle(): void
    {
        $this->line("Hello ".$this->argument('name'));
    }
}
