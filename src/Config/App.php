<?php

use Myerscode\Acorn\Framework\Log\NullLogger;
return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'input' => '',
    'output' => '',
    'logger' => NullLogger::class
];
