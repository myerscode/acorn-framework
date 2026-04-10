<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Log;

use Psr\Log\NullLogger as BaseLogger;

class NullLogger extends BaseLogger implements LogInterface
{

}
