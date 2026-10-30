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

## Calling Other Commands

You can call other commands from within a command using the `call()` method:

```php
public function handle(): int
{
    $exitCode = $this->call('other:command', ['arg' => 'value']);

    return $exitCode;
}
```
