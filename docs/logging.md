# Logging

The Foundation Logger provides PSR-3 compliant file-based logging for Acorn CLI applications. Log files are written to a configurable directory and support multiple rotation strategies.

## Usage

### Initialisation

```php
use Myerscode\Acorn\Foundation\Log\Logger;
use Myerscode\Acorn\Foundation\Log\LogRotation;

$logger = new Logger('/path/to/logs', LogRotation::Daily);
```

### Logging Messages

```php
$logger->info('Informational message');
$logger->error('Error occurred', ['exception' => $e->getMessage()]);
$logger->debug('Processing item', ['id' => $item->id]);
```

All standard PSR-3 log levels are supported: `emergency`, `alert`, `critical`, `error`, `warning`, `notice`, `info`, `debug`.

Invalid log levels will throw a `\Psr\Log\InvalidArgumentException`.

## Log Rotation

| Strategy | File Pattern | Description |
|----------|-------------|-------------|
| `Single` | `acorn.log` | All logs in one file |
| `Daily` | `acorn-YYYY-MM-DD.log` | One file per day |
| `Weekly` | `acorn-week-YYYY-WW.log` | One file per week |
| `Monthly` | `acorn-YYYY-MM.log` | One file per month |
| `Yearly` | `acorn-YYYY.log` | One file per year |

## Configuration

The logger is configured through the `LogProvider` service provider. Override the logger binding in your app's service provider to customise:

```php
use Myerscode\Acorn\Foundation\Log\Logger;
use Myerscode\Acorn\Foundation\Log\LogRotation;

$this->getContainer()->add('logger', fn () => new Logger(
    config('app.dir.logs'),
    LogRotation::Daily,
    'myapp'  // custom log prefix
));
```

## Security

- Log messages and context values are sanitised to prevent log injection attacks. Newlines in messages or context are replaced with spaces to ensure one log entry per line.
- The log directory is created automatically if it does not exist.

## Custom Log Prefix

```php
$logger = new Logger('/path/to/logs', LogRotation::Single, 'myapp');
// Writes to: /path/to/logs/myapp.log
```
