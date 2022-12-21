<?php

namespace Myerscode\Acorn\Framework\Cache;

use Psr\SimpleCache\CacheInterface as CacheDriverInterface;

interface DriverInterface extends CacheDriverInterface
{
    /**
     * Return a total number of values stored in the cache
     *
     * @return int
     */
    public function count(): int;
}
