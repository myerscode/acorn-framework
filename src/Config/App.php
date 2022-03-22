<?php

use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Framework\Console\Input;
use Myerscode\Acorn\Framework\Console\Output;
use Myerscode\Acorn\Framework\Log\NullLogger;

return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'input' => Input::class,
    'output' => Output::class,
    'logger' => NullLogger::class,
    'queue' => SynchronousQueue::class,
];
