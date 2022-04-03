<?php

namespace Tests;

use Myerscode\Acorn\Testing\AcornTestCase;

class BaseTestCase extends AcornTestCase
{
    public function runningFrom(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}
