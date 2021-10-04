<?php

return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'logger' => \Myerscode\Acorn\Framework\Log\NullLogger::class
];
