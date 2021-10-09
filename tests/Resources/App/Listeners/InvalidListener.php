<?php

namespace Tests\Resources\App\Listeners;

use Myerscode\Acorn\Framework\Events\Listener;

class InvalidListener extends Listener
{
    protected $listensFor = 123;
}
