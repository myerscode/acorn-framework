# Events

Listeners are there, waiting for an event to happen - they will have their `handler` called, when its events are fired.


Creating an event listener in the `Listeners` directory
will have them automatically loaded in by your Acorn app.


## Creating an event using the CLI

The command takes a listener name, and list of comma delimited events

```php
acorn make:listener SendAlert --events=error,offline
```

## Creating a class by hand

Extend the base event listener class: `Myerscode\Acorn\Framework\Events\Listener`

```php
<?php

namespace App\Listeners;

use Myerscode\Acorn\Framework\Events\Event;
use Myerscode\Acorn\Framework\Events\Listener;

class SendAlert extends Listener
{
    protected $listensFor = [
        'error',
        'offline'
    ];

    public function handle(Event $event)
    {
        //
    }
}
```
