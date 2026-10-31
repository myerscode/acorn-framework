# Commands

Commands are the core building blocks of an Acorn CLI application. Each command extends the base `Command` class and implements a `handle()` method.

## Creating a Command

```php
<?php

namespace App\Commands;

use Myerscode\Acorn\Framework\Console\Command;

class GreetCommand extends Command
{
    protected string $signature = 'greet {name}';

    protected string $description = 'Greet someone by name';

    public function handle(): int
    {
        $this->line('Hello ' . $this->argument('name'));

        return 0;
    }
}
```

## Exit Codes

The `handle()` method returns an integer exit code:
- `0` — success
- Any non-zero value — failure

```php
public function handle(): int
{
    if (!$this->isValid()) {
        $this->error('Validation failed');
        return 1;
    }

    $this->success('Done');
    return 0;
}
```

## Signature

Options and Arguments are wrapped in braces, and can be used to create powerful input parameters for commands.

```php
corgi:call {name}
```

### Arguments

Arguments can be required, have default values or be optional.

When creating your command you define the commands attributes by setting the `$signature` property.

#### Required argument
A required argument is defined by giving it a name wrapped in `{}`

```php
// a required name argument
protected string $signature = 'corgi:call {name}'
```

#### Default value argument
You can give an argument a default value by setting it in the brackets!

```php
// optional name argument with a default value set
protected string $signature = 'corgi:call {name=gerald}'
```

#### Optional arguments
Sometimes you want to make arguments optional, do this by putting a `?` after the arguments name

```php
// optional name argument
protected string $signature = 'corgi:call {name?}'
```

### Options
Options are another form of user input and are a great way of add functionality to your application.

There are two types of options: those that receive a value and those that act as a true/false flag.

All options are prefixed by two hyphens `--` and are also defined between `{}`.

#### Boolean options

If an option is passed to the command, the value of the option will be `true`.
If it is not, the value will be `false`.

```php
// a true/false option
protected string $signature = 'corgi:action {--teatime}'
```

#### Options requiring user input
If the user must specify a value for an option, you should suffix the option name with a `=`.
```php
// an option requiring user input
protected string $signature = 'corgi:action {--fluff=}'
```

#### Options with default value
You may assign default values to options by specifying the default value after the `=` on the option name.
If no option value is set when called, the default value will be used!
```php
// an option with a default value
protected string $signature = 'corgi:action {--fluff=butt}'
```

## Reading Input

Read arguments and options with `argument()` and `option()` (both return
strings), a boolean switch with `flag()`, or a typed accessor that coerces the
value for you:

```php
protected string $signature = 'report {count} {--rate=} {--verbose}';

public function handle(): int
{
    $count = $this->integerArgument('count');     // int|null
    $rate  = $this->floatOption('rate', 1.0);     // float
    $loud  = $this->flag('verbose');              // bool — true only if --verbose was passed

    // ...
    return 0;
}
```

Available accessors:

| String | Integer | Float | Boolean |
| --- | --- | --- | --- |
| `argument()` | `integerArgument()` | `floatArgument()` | `booleanArgument()` |
| `option()` | `integerOption()` | `floatOption()` | `flag()` |

A typed accessor returns its default (or `null`) when the value is absent or not
numeric, so you never parse strings by hand.

## Output

Besides the message helpers (`line`, `info`, `success`, `warning`, `error`,
`comment`, `note`, `table`), you can emit machine-readable JSON — handy for a
`--format=json` flag:

```php
protected string $signature = 'users {--format=text}';

public function handle(): int
{
    $users = [['name' => 'Rupert'], ['name' => 'Gerald']];

    if ($this->option('format') === 'json') {
        $this->output->json($users);          // pretty-printed
    } else {
        $this->output->table(['Name'], array_map(fn ($u) => [$u['name']], $users));
    }

    return 0;
}
```

Pass `$pretty = false` for compact single-line JSON suitable for piping.

## Branding

The CLI's name and version (shown by `list` and `--version`) come from the
`app.name` and `app.version` config keys, so your app can brand itself:

```php
// app/Config/App.php
return [
    'name' => 'My Tool',
    'version' => '2026.1.0',
];
```

## Failing a Command

Return a non-zero exit code from `handle()`, or call `fail()` to abort
immediately from anywhere in the command. `fail()` renders the message and exits
with the given code (default `1`):

```php
public function handle(): int
{
    if (! $this->isReady()) {
        $this->fail('Not ready', 2); // renders "Not ready", process exits 2
    }

    return 0;
}
```

## Calling Other Commands

Call another command with `call()`, or `callSilently()` to suppress its output:

```php
public function handle(): int
{
    $exitCode = $this->call('other:command', ['arg' => 'value']);

    $this->callSilently('cache:warm'); // runs, but prints nothing

    return $exitCode;
}
```

## Handling Signals

For long-running commands, trap `SIGINT` (Ctrl-C) / `SIGTERM` for a graceful
shutdown. Implement `SignalableCommandInterface`, `use InteractsWithSignals`, and
register handlers with `onSignal()`:

```php
use Myerscode\Acorn\Framework\Console\Command;
use Myerscode\Acorn\Framework\Console\InteractsWithSignals;
use Symfony\Component\Console\Command\SignalableCommandInterface;

class WatchCommand extends Command implements SignalableCommandInterface
{
    use InteractsWithSignals;

    protected string $signature = 'watch';

    public function handle(): int
    {
        $this->onSignal(SIGINT, function (): int {
            $this->output->warning('Stopping cleanly...');

            return 0; // exit code; return false to keep running
        });

        while (true) {
            // ... long-running work ...
        }
    }
}
```

Requires the `pcntl` extension; without it the handlers simply never fire.
