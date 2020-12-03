<?php

namespace Myerscode\Acorn\Framework\Events;

use League\Container\Container as DependencyManager;

class Planner
{

    protected DependencyManager $container;

    private Bus $bus;

    public function __construct(DependencyManager $container, Bus $bus)
    {
        $this->container = $container;
        $this->bus = $bus;
    }

    public function bindEventFromListener(AcornEventListener $listener)
    {
        $events = $listener->listensFor();

        foreach ($events as $event) {
            if (is_subclass_of($event, AcornEvent::class, true)) {
                $constructedEvent = $this->container->get($event);
                $this->bus->addListener((string) $constructedEvent, $listener);
            }
        }
    }

    public function bindEventsFromRegister(AcornEventRegister $provider)
    {
        $events = $provider->getEvents();

        foreach ($events as $event => $listeners) {
            foreach ($listeners as $listener) {
                if (is_subclass_of($event, AcornEvent::class, true)) {
                    $callableListener = $this->container->get($listener);
                    if ($callableListener instanceof AcornEventListener) {
                        $this->bus->addListener((string) new $event, $callableListener);
                    }
                }
            }
        }
    }
}
