<?php

namespace Myerscode\Acorn\Framework\Console;

trait InteractsWithInput
{
    /**
     * The input interface implementation.
     */
    protected ConsoleInputInterface $input;

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
            return $this->input->getOption($key);
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
        return $this->input->parameters();
    }
}
