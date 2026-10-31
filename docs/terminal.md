# Terminal

If you need to execute external commands outside the running PHP command, you can use a `Terminal` to build and
run that process.

## Running a Command

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->run('ls -la');
```

`run()` executes the command string through the system shell (`/bin/sh -c`),
so shell features like pipes, redirects and globbing work. `runShell()` is an
explicit alias for the same behaviour — prefer it at call sites where you want
the shell semantics (and their risk) to be obvious:

```php
terminal()->runShell('cat access.log | grep ERROR');
```

> **Security:** because `run()` / `runShell()` go through the shell,
> interpolating user-controlled values into the command enables shell
> injection. For anything built from user input, use `runSafe()` (below), which
> bypasses the shell entirely.

## Safe Execution (No Shell Interpretation)

When command arguments may contain user-controlled values, use `runSafe()` to prevent shell injection. Arguments are passed directly to the process without shell interpretation:

```php
use function Myerscode\Acorn\Foundation\terminal;

// Safe — $userPath is never interpreted by the shell
$response = terminal()->runSafe(['ls', '-la', $userPath]);

// Safe — even metacharacters are treated as literals
$response = terminal()->runSafe(['echo', $untrustedInput]);
```

Use `runSafe()` whenever building commands from dynamic or user-provided values.

## Interacting with the Command Output

To interact with the output from the running command, pass in a callback to the run call.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->run('ls -la', fn($output) => var_dump($output));
```

If you want to display the output, then you can pass in a `DisplayOutput` instance:

```php
use function Myerscode\Acorn\Foundation\terminal;
use function Myerscode\Acorn\Foundation\output;

terminal()->run('ls -la', output());
```

Alternatively you can get the output from the response given by calling `output()`:

```php
use function Myerscode\Acorn\Foundation\terminal;

$response = terminal()->run('ls -la');

echo $response->output();
```

## Setting a Timeout

Set a timeout (in seconds) that will cause the command to fail if reached.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->timeout(10)->run('ls -la');
```

## Number of Attempts

If a command fails, you can set how many times the terminal will retry.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->retries(5)->run('ls -la');
```

## Sleep Between Attempts

When retrying commands, you may want to throttle requests (e.g. for failed API calls). You can tell the terminal to sleep a number of seconds before each retry.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->sleep(15)->run('ls -la');
```

## Environment Variables

When you run a command, it will have access to the same environment variables as the current PHP process.

If you would like to run a command with extra environment variables, use the `withEnvironmentVariables` method. It accepts an array of key-value pairs that will be made available to the command.

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->withEnvironmentVariables(['DIRECTORY' => 'app'])->run('ls -la $DIRECTORY');
```

## Async Execution

Run a process in the background without blocking:

```php
use function Myerscode\Acorn\Foundation\terminal;

terminal()->async('long-running-command');
```
