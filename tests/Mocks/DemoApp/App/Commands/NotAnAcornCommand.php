<?php

namespace Tests\Mocks\DemoApp\App\Commands;

class NotAnAcornCommand
{
    protected string $description = 'A simple text command with text output.';

    protected string $signature = 'simple {name : Say hello}';

    public function handle(): void
    {
        $this->line('Hello '.$this->argument('name'));
    }
}
