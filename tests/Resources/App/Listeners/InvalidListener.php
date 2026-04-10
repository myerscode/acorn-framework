<?php

declare(strict_types=1);

namespace Tests\Resources\App\Listeners;

class InvalidListener
{
    protected string|array $listensFor = '123';
}
