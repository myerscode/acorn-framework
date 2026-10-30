<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Cache;

use Psr\SimpleCache\CacheInterface as CacheDriverInterface;

interface DriverInterface extends CacheDriverInterface
{
    /**
     * Return a total number of values stored in the cache
     */
    public function count(): int;
}
