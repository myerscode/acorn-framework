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
    public final const HIGH = 100;

    /**
     * Normal priority.
     *
     * @const int
     * @var int
     */
    public final const NORMAL = 0;

    /**
     * Low priority.
     *
     * @const int
     * @var int
     */
    public final const LOW = -100;
}
