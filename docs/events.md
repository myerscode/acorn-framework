# Events

Acorn has a unified event system for dispatching and listening to events within your application.

## Dispatching Events

Create an event object and dispatch it for listeners to consume:

```php
use function Myerscode\Acorn\Foundation\dispatch;

$event = new AppEvent();
dispatch($event);
```

## Emitting Events

You can emit events by class name or by a custom string name. When emitting by class name, the event will be instantiated automatically:

```php
use function Myerscode\Acorn\Foundation\emit;

emit('acorn.build.before');

emit(BeforeBuildEvent::class);

// With parameters passed to the event constructor
emit(BuildEvent::class, [$buildTarget]);
```

## Event Classes

Events extend the base `Event` class:

```php
<?php

namespace App\Events;

use Myerscode\Acorn\Framework\Events\Event;

class BuildCompleted extends Event
{
    public function __construct(public readonly string $target)
    {
    }
}
```

## Propagation

Events support propagation stopping. When a listener calls `stopPropagation()`, subsequent listeners for that event will not be called:

```php
public function handle(EventInterface $event): void
{
    $event->stopPropagation();
}
```

## Unified Dispatcher

The Acorn Dispatcher implements Symfony's `EventDispatcherInterface`, which means:

- It handles both Symfony Console events and Acorn application events in a single dispatcher
- No bridging layer is needed between Symfony Console and your application events
- You can register Symfony event subscribers directly on the Acorn dispatcher

## Listening for Any Event

To listen for any event fired (useful for tracing or logging), use one of the catch-all keywords `*`, `all`, or `any` as the event name:

```php
class DetailedLoggingListener extends Listener
{
    public function handle(EventInterface $event): void
    {
        // Called for every event
    }

    public function listensFor(): array|string
    {
        return '*';
    }
}
```
