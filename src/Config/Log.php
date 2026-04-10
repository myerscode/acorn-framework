<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Config;

use Myerscode\Acorn\Foundation\Log\LogRotation;

return [
    'logDirectory' => '${base}/logs', // default log directory, can be overridden
    'logPrefix' => 'acorn', // custom log file prefix
    'rotation' => LogRotation::Single, // options: Single, Daily, Weekly, Monthly, Yearly
];
