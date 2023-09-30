<?php

namespace Tests;

use Myerscode\Acorn\Testing\AcornTestCase;

class BaseTestCase extends AcornTestCase
{
    protected string $appDirectory = 'tests/Resources/App';

    public static function createTempDirectory($name): string
    {
        // Create a unique directory in the system's temporary directory
        $temp_dir = sys_get_temp_dir() . '/' . $name . '_' . time() . '_' . random_int(0, mt_getrandmax());

        mkdir($temp_dir);

        return $temp_dir;
    }

    public function runningFrom(): string
    {
        return dirname(__DIR__);
    }
}
