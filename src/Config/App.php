<?php

namespace Myerscode\Acorn\Config;

return [
    'root' => '${root}',
    'base' => '${base}',
    'dir' => [
        'commands' => '${base}/Commands',
        'events' => '${base}/Events',
        'listeners' => '${base}/Listeners',
        'providers' => '${base}/Providers',
    ],
    'input' => \Myerscode\Acorn\Foundation\Console\Input\Input::class,
    'output' => \Myerscode\Acorn\Foundation\Console\Display\DisplayOutput::class,
    'logger' => \Myerscode\Acorn\Framework\Log\NullLogger::class,
    'queue' => \Myerscode\Acorn\Foundation\Queue\SynchronousQueue::class,
    'cache' => [
        'driver' => \Myerscode\Acorn\Foundation\Cache\Driver\RuntimeCache::class,
        'namespace' => 'acorn',
        'config' => [],
    ],
];
