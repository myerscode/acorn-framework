# Listeners

Listeners wait for an event to happen and have their `handle` method called when their registered events are fired.

Creating a listener class in the `Listeners` directory will have it automatically loaded by your Acorn app.

## Creating a Listener

Extend the base listener class and implement the `listensFor()` method to declare which events the listener handles:

```php
<?php

namespace App\Listeners;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;

class SendAlert extends Listener
{
    public function handle(EventInterface $event): void
    {
        // Handle the event
    }

    public function listensFor(): array|string
    {
        return [
            'error',
            'offline',
        ];
    }
}
```

You can listen for a single event by returning a string:

```php
public function listensFor(): array|string
{
    return ErrorEvent::class;
}
```

Or multiple events by returning an array:

```php
public function listensFor(): array|string
{
    return [
        ErrorEvent::class,
        'custom.event.name',
    ];
}
```

## Listening for All Events

To listen for every event fired (useful for tracing or logging), return one of the catch-all keywords: `*`, `all`, or `any`:

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

## Queueable Listeners

A listener can be queued for deferred processing by setting the `$shouldQueue` property:

```php
class SlowProcessListener extends Listener
{
    protected bool $shouldQueue = true;

    public function handle(EventInterface $event): void
    {
        // This will be processed via the queue
    }

    public function listensFor(): array|string
    {
        return SlowProcessEvent::class;
    }
}
```

## Exception Isolation

If a listener throws an exception during dispatch, it will not prevent other listeners for the same event from executing. Failed listeners are silently caught to ensure robustness.
