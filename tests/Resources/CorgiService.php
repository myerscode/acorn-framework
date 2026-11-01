<?php

declare(strict_types=1);

namespace Tests\Resources;

/**
 * A plain service with no constructor dependencies, used to prove handle()
 * parameters are autowired from the container.
 */
final class CorgiService
{
    public function name(): string
    {
        return 'Rupert';
    }
}
