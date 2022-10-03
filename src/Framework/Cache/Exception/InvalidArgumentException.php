<?php

namespace Myerscode\Acorn\Framework\Cache\Exception;

use InvalidArgumentException as CoreInvalidArgumentException;
use Psr\SimpleCache\InvalidArgumentException as PsrCacheInvalidArgumentException;

class InvalidArgumentException extends CoreInvalidArgumentException implements PsrCacheInvalidArgumentException
{
    //
}
