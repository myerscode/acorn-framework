<?php

namespace Tests\Resources\App\Listeners;

use Myerscode\Acorn\Framework\Events\Listener;

class ListensForArrayListener extends Listener
{
    protected $listensFor = [
        'test.event.one',
        'test.event.two',
    ];
}
