<?php

declare(strict_types=1);

namespace Tests\Mocks\DemoApp\App\Listeners;

class InvalidListener
{
    protected string|array $listensFor = '123';
}
