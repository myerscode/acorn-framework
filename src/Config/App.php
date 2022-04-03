<?php

namespace Myerscode\Acorn\Config;

return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'input' => \Myerscode\Acorn\Foundation\Console\Input::class,
    'output' => \Myerscode\Acorn\Foundation\Console\Output::class,
    'logger' => \Myerscode\Acorn\Framework\Log\NullLogger::class,
    'queue' => \Myerscode\Acorn\Foundation\Queue\SynchronousQueue::class,
];
