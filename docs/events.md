# Events

You probably want to emit events when things happen within your application!
Acorn has a convenient way to dispatch events within your command controllers.

## Dispatch Events

You can create an event object, and dispatch if for listeners to consume.

```php
$event = new AppEvent();

disaptch($event);
```

## Emitting events

Alternatively - if may just want to emit the event, for subscribers to respond too.

```php
emit('acorn.build.before');

emit(BeforeBuildEvent:class);
```

## Listening for events

## Event Register

### Listen for any event

In the case that you want to listen for any event fired, for example if you want to implement some form of tracing or
logging -
you can create a listener and give it one of the following keywords `*`, `all`, `any` as an event name.

This when an any event is fired, it will also be sent to that listener, as well as the ones explicitly told to listen
for that event.

An example lister

```php 
class DetailedLoggingListener extends Listener
{
    protected string|array $listensFor = '*'
}
```
