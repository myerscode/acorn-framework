<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

interface SubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     */
    public function getSubscribedEvents(): array;
}
