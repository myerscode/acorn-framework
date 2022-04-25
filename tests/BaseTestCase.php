<?php

namespace Tests;

use Myerscode\Acorn\Testing\AcornTestCase;

class BaseTestCase extends AcornTestCase
{
    protected string $appDirectory = 'tests/Resources/App';

    public function runningFrom(): string
    {
        return dirname(__DIR__);
    }
}
