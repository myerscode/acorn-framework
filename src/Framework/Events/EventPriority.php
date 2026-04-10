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
    public final const int HIGH = 100;

    /**
     * Normal priority.
     *
     * @const int
     */
    public final const int NORMAL = 0;

    /**
     * Low priority.
     *
     * @const int
     */
    public final const int LOW = -100;
}
