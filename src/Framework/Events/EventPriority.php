<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

class EventPriority
{
    /**
     * High priority.
     *
     * @const int
     */
    final public const int HIGH = 100;

    /**
     * Low priority.
     *
     * @const int
     */
    final public const int LOW = -100;

    /**
     * Normal priority.
     *
     * @const int
     */
    final public const int NORMAL = 0;
}
