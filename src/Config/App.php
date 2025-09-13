<?php

namespace Myerscode\Acorn\Config;

use Myerscode\Acorn\Foundation\Console\Input\Input;
use Myerscode\Acorn\Foundation\Console\Display\DisplayOutput;
use Myerscode\Acorn\Framework\Log\NullLogger;
use Myerscode\Acorn\Foundation\Queue\SynchronousQueue;
use Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache;

return [
    'root' => '${root}',
    'base' => '${base}',
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
        'providers' => '${base}/Providers',
    ],
    'input' => Input::class,
    'output' => DisplayOutput::class,
    'logger' => NullLogger::class,
    'queue' => SynchronousQueue::class,
    'cache' => [
        'driver' => RuntimeCache::class,
        'namespace' => 'acorn',
        'config' => [],
    ],
];
