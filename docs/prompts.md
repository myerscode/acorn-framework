# Prompts

Commands can interact with users through prompts. These methods are available on any class extending `Command`.

## ask

Prompt for free-text input:

```php
$name = $this->ask('What is your name');
```

With a default value:

```php
$name = $this->ask('What is your name', 'World');
```

## confirm

Prompt for a yes/no confirmation:

```php
if ($this->confirm('Do you want to continue')) {
    // ...
}
```

The second parameter sets the default (`true` = yes, `false` = no):

```php
// defaults to "no" if the user just presses enter
$this->confirm('Are you sure', false);
```

## choice

Present a list of options to choose from:

```php
$colour = $this->choice('Pick a colour', ['red', 'green', 'blue']);
```

With a default:

```php
$colour = $this->choice('Pick a colour', ['red', 'green', 'blue'], 'blue');
```

Allow multiple selections:

```php
$colours = $this->choice('Pick colours', ['red', 'green', 'blue'], null, true);
// returns an array of selected values
```

## secret

Prompt for sensitive input (input is hidden):

```php
$password = $this->secret('Enter your password');
```

## anticipate

Prompt with autocomplete suggestions, while still allowing freeform input:

```php
$framework = $this->anticipate('Which framework', ['acorn', 'laravel', 'symfony']);
```

With a default:

```php
$framework = $this->anticipate('Which framework', ['acorn', 'laravel', 'symfony'], 'acorn');
```

The user is not limited to the suggestions — they can type any value.
