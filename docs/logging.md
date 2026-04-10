# Foundation Logger

The Foundation Logger provides file-based logging for Acorn CLI applications. Log files are written to a configurable directory and support multiple rotation strategies.

## Usage

### Initialization

```php
use Myerscode\Acorn\Foundation\Log\Logger;
use Myerscode\Acorn\Foundation\Log\LogRotation;

$logger = new Logger('/path/to/logs', LogRotation::Daily); // or SingleFile, Weekly, Monthly, Yearly
```

### Logging

```php
$logger->info('Informational message');
$logger->error('Error message', ['exception' => $e]);
```

## Configuration

- **logDirectory**: Directory where log files are stored.
- **rotation**: Log rotation strategy (SingleFile, Daily, Weekly, Monthly, Yearly).

## Log Rotation Enum

- `Single`: All logs in one file (`acorn.log`).
- `Daily`: One file per day (`acorn-YYYY-MM-DD.log`).
- `Weekly`: One file per week (`acorn-week-YYYY-WW.log`).
- `Monthly`: One file per month (`acorn-YYYY-MM.log`).
- `Yearly`: One file per year (`acorn-YYYY.log`).

## Example

```php
$config = new \Myerscode\Acorn\Foundation\Config\Log('/var/logs/acorn', \Myerscode\Acorn\Foundation\Log\LogRotation::Monthly);
$logger = new \Myerscode\Acorn\Foundation\Log\Logger($config->logDirectory, $config->rotation);
```

## Testing

Unit tests for the logger are available in `tests/Foundation/Log/LoggerTest.php`.
