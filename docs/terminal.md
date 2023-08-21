# Terminal

If you need to execute external commands outside the running PHP command, you can use a `Terminal` to build and
run that process.

## Running a command

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->run('ls -la')
```

## Interacting with the command output

To interact with the output from the running command, pass in a callback to the run call.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->run('ls -la', fn($output) => var_dump($output))
```

If you want to display the output, then you can pass in a `DisplayOutput` instance

```php
use function Myerscode\Acorn\Foundation\terminal;
use function Myerscode\Acorn\Foundation\output;

terminal()->run('ls -la', output())
```

Alternatively you can get the output from the response given by calling the `output()`

```php
use function Myerscode\Acorn\Foundation\terminal;
use function Myerscode\Acorn\Foundation\output;

$response= terminal()->run('ls -la');

echo $response->output()
```

## Setting a timeout

Set a timeout (in seconds) that will cause the command to fail if reached.

`Default: None`

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->timeout(10)->run('ls -la');
```

## Number of attempts

If a command fails, you can set how many times the terminal will rerun.

`Default: 1`

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->retries(5)->run('ls -la');
```

## Sleep between attempts

When retying commands, you may want to wait and throttle those requests (e.g. for failed API calls). You can tell the
terminal
to sleep a number of seconds before each attempt is run.

`Default: 0`

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->sleep(15)->run('ls -la');
```
