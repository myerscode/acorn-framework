# Pipelines

Pipelines can be used perform tasks before or after on a "thing" - passing the object down through a series of pipes. 

A common use of pipes is to create middleware for HTTP requests, where you would want to do actions as the 
request comes in (_**before**_) handle the request in a controller (_**in the core**_) then perform some tasks once the 
request is processed (**_after_**).

Another great use could be to process an object, hydrating properties on it and running tasks based on its state.

## Usage

```php 
$pipes = [
    BeforePipe::class,
    AfterPipe::class,
];

$pipeline = new Pipeline($pipes);
                  
$result = $pipeline->flush($object);
```

## Pipes

```php
<?php

namespace App\Pipes;
 
use Closure;
 
class BeforePipe
{
    public function handle($request, Closure $next)
    {
        // Perform action
 
        return $next($request);
    }
}
```

```php
<?php
 
namespace App\Pipes;
 
use Closure;
 
class AfterPipe
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
 
        // Perform action
 
        return $response;
    }
}
```

## Pipe Groups

Sometimes you may want to group several pipes under a name to make them easier to reuse and manage. To do that, make use 
of the `LineManager` where you can define pipelines, which can then be used by calling them by name.

You are able to pass through multiple pipelines, which will send the object through each pipeline (doing all before/after pipes) 
then passing it onto the next named pipe.

```php 
$lineManager = new LineManager();

$lineManager->setPipeline('setup', [
    SanatizePipe::class,
    ValidatePipe::class,
    HydratePipe::class,
]);

$lineManager->setPipeline('order', [
    LogPipe:class,
    SaveToDatabasePipe:class,
    EmailPipe:class,
]);

// through a single pipe
$response = $lineManager->send($day)->through('logging');

// through multiple pipes
$response = $lineManager->send($day)->through(['logging', 'order']);
```
