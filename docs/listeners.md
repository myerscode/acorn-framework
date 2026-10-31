# Listeners

Listeners wait for an event to happen and have their `handle` method called when their registered events are fired.

Creating a listener class in the `Listeners` directory will have it automatically loaded by your Acorn app.

## Creating a Listener

Extend the base listener class and implement the `listensFor()` method to declare which events the listener handles. Put your logic in `handle()`:

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

## `handle()` or `__invoke()`

The dispatcher calls your listener's `handle()` method. For convenience you may
instead define an `__invoke()` method — the base `Listener::handle()` bridges to
it when present, so either convention works:

```php
class SendAlert extends Listener
{
    public function __invoke(EventInterface $event): void
    {
        // Equivalent to overriding handle()
    }

    public function listensFor(): array|string
    {
        return ErrorEvent::class;
    }
}
```

If you override `handle()`, that takes precedence.

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

If a listener throws during dispatch, the failure is isolated so the remaining
listeners for the same event still run. The exception is **logged** (via the
application logger) rather than swallowed silently, so a broken listener does
not disappear without a trace.
