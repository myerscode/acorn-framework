# Error Handling

Acorn provides a global exception handler that catches all unhandled exceptions, reports them (via logging), and renders them to the console. You can customise every part of this process.

The handler is registered automatically during application boot. PHP errors and warnings are converted into exceptions via Symfony's `ErrorHandler`.

## How It Works

When an exception is thrown during command execution:

1. **Report** — the exception is logged (unless suppressed)
2. **Catch handlers** — registered handlers run; if one returns `true`, rendering is skipped
3. **Render** — the exception is formatted and output to stderr

## Binding Logic to Errors

Use `catch()` to register handlers that run when specific exceptions occur. Handlers can perform any logic — retry operations, send notifications, clean up resources, or suppress the error output entirely.

```php
$handler = $kernel->exceptionHandler();

$handler->catch(ConnectionException::class, function (ConnectionException $e): ?bool {
    // Attempt reconnection, notify monitoring, etc.
    retry_connection();

    return true; // Handled — don't render the error
});
```

### Return Values

- Return `true` — the exception is handled, rendering is skipped
- Return `false` or `null` — normal rendering continues

### Multiple Handlers

You can register multiple handlers for the same exception type. They run in registration order until one returns `true`:

```php
$handler->catch(TimeoutException::class, function (TimeoutException $e): ?bool {
    log_timeout($e);

    return null; // Let the next handler decide
});

$handler->catch(TimeoutException::class, function (TimeoutException $e): bool {
    notify_ops($e);

    return true; // Handled
});
```

### Subclass Matching

Handlers match subclasses. A handler for `Exception` will catch any exception:

```php
$handler->catch(Exception::class, function (Exception $e): ?bool {
    // Runs for ALL exceptions
    send_to_bugtracker($e);

    return null; // Still render normally
});
```

## Suppressing Reports

Prevent certain exceptions from being logged:

```php
$handler->dontReport(
    ValidationException::class,
    UserCancelledException::class,
);
```

## Custom Reporters

Override how specific exceptions are reported:

```php
$handler->reportUsing(PaymentFailedException::class, function (PaymentFailedException $e): void {
    external_alert_service($e->getMessage());
});
```

## Custom Renderers

Override how specific exceptions are displayed:

```php
use Symfony\Component\Console\Output\OutputInterface;

$handler->renderUsing(ValidationException::class, function (OutputInterface $output, ValidationException $e): void {
    $output->writeln('<comment>Validation failed:</comment>');
    foreach ($e->errors() as $error) {
        $output->writeln('  - ' . $error);
    }
});
```

## Default Rendering

When no custom renderer is registered, exceptions render as:

```
  RuntimeException

  Something went wrong
```

With `-v` (verbose), the file and line are shown. With `-vv`, the full stack trace is included.

## Accessing the Handler

From the kernel:

```php
$handler = $kernel->exceptionHandler();
```

From the container:

```php
use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;

$handler = container()->get(ExceptionHandlerInterface::class);
```

## Replacing the Handler

To use your own handler, bind it to the interface in a service provider:

```php
use Myerscode\Acorn\Framework\Exceptions\ExceptionHandlerInterface;
use Myerscode\Acorn\Framework\Providers\ServiceProvider;

class AppExceptionHandlerProvider extends ServiceProvider
{
    protected $provides = [ExceptionHandlerInterface::class];

    public function register(): void
    {
        $this->getContainer()->addShared(
            ExceptionHandlerInterface::class,
            fn () => new MyCustomHandler($this->getContainer()->get('logger')),
        );
    }
}
```

Register your provider after the framework's `ExceptionHandlerProvider` to override it.
