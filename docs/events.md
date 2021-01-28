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
