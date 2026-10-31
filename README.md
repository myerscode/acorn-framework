[![Latest Stable Version](https://poser.pugx.org/myerscode/acorn-framework/v/stable)](https://packagist.org/packages/myerscode/acorn-framework)
[![Total Downloads](https://poser.pugx.org/myerscode/acorn-framework/downloads)](https://packagist.org/packages/myerscode/acorn-framework)
[![PHP Version Require](http://poser.pugx.org/myerscode/acorn-framework/require/php)](https://packagist.org/packages/myerscode/acorn-framework)
[![License](https://poser.pugx.org/myerscode/acorn-framework/license)](https://github.com/myerscode/acorn-framework/blob/main/LICENSE)
[![Tests](https://github.com/myerscode/acorn-framework/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/myerscode/acorn-framework/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/myerscode/acorn-framework/graph/badge.svg)](https://codecov.io/gh/myerscode/acorn-framework)

# Acorn Framework

> A framework for creating powerful PHP CLI applications.

Acorn is the backbone of the Acorn ecosystem: it extends Symfony Console with a
dependency-injection container, automatic command/provider/listener discovery,
an event dispatcher, config, cache, queue, pipelines, a process/terminal
wrapper, a global exception handler, logging, interactive prompts and progress
bars.

## Requirements

- PHP 8.5 or higher

## Getting started

Most people start from the [application skeleton](https://github.com/myerscode/acorn):

```bash
composer create-project myerscode/acorn my-app --remove-vcs
cd my-app
php acorn
```

To use the framework directly:

```bash
composer require myerscode/acorn-framework
```

A command extends the base `Command`, declares a `$signature`, and implements
`handle()`:

```php
use Myerscode\Acorn\Framework\Console\Command;

class GreetCommand extends Command
{
    protected string $signature = 'greet {name} {--loud}';

    protected string $description = 'Greet someone';

    public function handle(): int
    {
        $greeting = 'Hello ' . $this->argument('name');

        $this->output->writeln($this->flag('loud') ? strtoupper($greeting) : $greeting);

        return 0;
    }
}
```

## Documentation

Guides for each subsystem live in [`docs/`](docs/): commands, events, listeners,
cache, logging, pipelines, prompts, progress, terminal, error handling and
[optimization](docs/optimization.md).

## Issues

Bug reports and feature requests can be submitted on the [Github Issue Tracker](https://github.com/myerscode/acorn-framework/issues).

## Contributing

Contributions are welcome! Please see [contributing.md](https://github.com/myerscode/docs/blob/master/contributing.md) for guidelines on how to contribute to this project.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
