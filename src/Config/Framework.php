<?php

namespace Myerscode\Acorn\Config;

use Myerscode\Acorn\Foundation\Providers\ConsoleProvider;
use Myerscode\Acorn\Foundation\Providers\LogProvider;
use Myerscode\Acorn\Foundation\Providers\QueueProvider;

return [
    'dir' => [
        'commands' => '${src}/Foundation/Commands',
        'events' => '${src}/Foundation/Events',
        'listeners' => '${src}/Foundation/Listeners',
    ],
    'providers' => [
        ConsoleProvider::class,
        LogProvider::class,
        QueueProvider::class,
    ],
];
