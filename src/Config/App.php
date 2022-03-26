<?php

namespace Myerscode\Acorn\Config;

use \Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use \Myerscode\Acorn\Framework\Console\Input;
use \Myerscode\Acorn\Framework\Console\Output;
use \Myerscode\Acorn\Framework\Log\NullLogger;
return [
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
    ],
    'input' => \Myerscode\Acorn\Framework\Console\Input::class,
    'output' => \Myerscode\Acorn\Framework\Console\Output::class,
    'logger' => \Myerscode\Acorn\Framework\Log\NullLogger::class,
    'queue' => \Myerscode\Acorn\Foundation\Queue\SynchronousQueue::class,
];
