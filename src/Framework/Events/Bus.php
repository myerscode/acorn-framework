<?php

namespace Myerscode\Acorn\Framework\Events;

use Myerscode\Acorn\Framework\Events\Exception\UnknownEventTypeException;

class Bus
{
    private Emitter $emitter;

    public function __construct(Emitter $emitter)
    {
        $this->emitter = $emitter;
    }

    protected function emitter(): Emitter
    {
        return $this->emitter;
    }

    public function addListener(string $eventName, AcornEventListener $callableListener): Bus
    {
        $this->emitter()->addListener($eventName, $callableListener);

        return $this;
    }

    public function emit(string $eventName, $params = null): Bus
    {
        if (class_exists($eventName)) {
            if (($event = new $eventName) instanceof AcornEvent) {
                $eventName = (string) $event;
            } else {
                throw new UnknownEventTypeException();
            }
        }

        $this->emitter()->emit($eventName, $params);

        return $this;
    }
}
