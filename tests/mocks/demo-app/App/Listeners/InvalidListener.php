<?php

namespace Tests\Resources\App\Listeners;

use Myerscode\Acorn\Framework\Events\Listener;

class InvalidListener extends Listener
{
    /**
     * @var int
     */
    protected $listensFor = 123;
}
