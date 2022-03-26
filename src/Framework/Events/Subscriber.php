<?php

namespace Myerscode\Acorn\Framework\Events;

class Subscriber implements SubscriberInterface
{
    protected array $events = [];

    /**
     * @return mixed[]
     */
    public function getSubscribedEvents(): array
    {
        return $this->events;
    }
}
