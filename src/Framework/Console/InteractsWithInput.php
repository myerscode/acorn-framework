<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console;

use Symfony\Component\Console\Input\InputInterface;

trait InteractsWithInput
{
    /**
     * The input interface implementation.
     */
    protected ConsoleInputInterface|InputInterface $input;

    /**
     * Get the value of a command argument.
     *
     * @return string|array<mixed>|null
     */
    public function argument(string $key, ?string $default = null): string|array|null
    {
        if ($this->hasArgument($key)) {
            return $this->input->getArgument($key);
        }

        return $default;
    }

    /**
     * Get all of the arguments passed to the command.
     *
     * @return array<string, mixed>
     */
    public function arguments(): array
    {
        return $this->input->getArguments();
    }

    /**
     * Determine whether a boolean flag option (VALUE_NONE) was supplied.
     *
     * Unlike hasOption(), which is true for any *defined* option, this
     * reports the actual truthiness of a switch such as `--force`. A
     * VALUE_NONE option resolves to true when passed and false otherwise,
     * so hasOption() can never be used to gate a flag.
     */
    public function flag(string $name): bool
    {
        if (!$this->input->hasOption($name)) {
            return false;
        }

        return $this->input->getOption($name) === true;
    }

    /**
     * Determine if the given argument is present.
     */
    public function hasArgument(string $name): bool
    {
        if (!$this->input->hasArgument($name)) {
            return false;
        }

        return !is_null($this->input->getArgument($name));
    }

    /**
     * Determine if the given option is present.
     */
    public function hasOption(string $name): bool
    {
        if (!$this->input->hasOption($name)) {
            return false;
        }

        return $this->input->getOption($name) !== null;
    }

    /**
     * Get the value of a command option.
     */
    public function option(string $key, ?string $default = null): ?string
    {
        if ($this->hasOption($key)) {
            $value = $this->input->getOption($key);

            return is_string($value) ? $value : $default;
        }

        return $default;
    }

    /**
     * Get all the options passed to the command.
     *
     * @return mixed[]
     */
    public function options(): array
    {
        return $this->input->getOptions();
    }

    /**
     * Get all the parameters passed to the command.
     *
     * @return mixed[]
     */
    public function parameters(): array
    {
        // parameters() is Acorn's own extension on ConsoleInputInterface; a
        // plain Symfony InputInterface (e.g. under the test harness) has none.
        return $this->input instanceof ConsoleInputInterface
            ? $this->input->parameters()
            : [];
    }
}
