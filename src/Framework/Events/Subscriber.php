<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Events;

class Subscriber implements SubscriberInterface
{
    /** @var array<string, string> */
    protected array $events = [];

    /**
     * @return array<string, string>
     */
    public function getSubscribedEvents(): array
    {
        return $this->events;
    }
}
