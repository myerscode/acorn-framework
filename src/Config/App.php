<?php

return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'input' => '',
    'output' => '',
    'logger' => \Myerscode\Acorn\Framework\Log\NullLogger::class
];
