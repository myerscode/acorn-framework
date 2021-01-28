<?php

namespace Myerscode\Acorn\Framework\Events;

class Subscriber implements SubscriberInterface
{
    protected array $events = [];

    public function getSubscribedEvents(): array
    {
        return $this->events;
    }
}
