<?php

namespace Myerscode\Acorn\Framework\Events;

class EventPriority
{
    /**
     * High priority.
     *
     * @const int
     * @var int
     */
    public const HIGH = 100;

    /**
     * Normal priority.
     *
     * @const int
     * @var int
     */
    public const NORMAL = 0;

    /**
     * Low priority.
     *
     * @const int
     * @var int
     */
    public const LOW = -100;
}
