<?php

namespace Myerscode\Acorn\Framework\Console;

trait InteractsWithInput
{
    /**
     * The input interface implementation.
     *
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected ConsoleInputInterface $input;

    /**
     * Determine if the given argument is present.
     */
    public function hasArgument(string|int $name): bool
    {
        if (!$this->input->hasArgument($name)) {
            return false;
        }
        return !is_null($this->input->getArgument($name));
    }

    /**
     * Get the value of a command argument.
     *
     * @param  string|null  $default
     *
     * @return string|array|null
     */
    public function argument(string $key, string $default = null)
    {
        if ($this->hasArgument($key)) {
            return $this->input->getArgument($key);
        }

        return $default;
    }

    /**
     * Get all of the arguments passed to the command.
     * @return mixed[]
     */
    public function arguments(): array
    {
        return $this->input->getArguments();
    }

    /**
     * Determine if the given option is present.
     */
    public function hasOption(string $name): bool
    {
        if (!$this->input->hasOption($name)) {
            return false;
        }
        return !empty($this->input->getOption($name));
    }

    /**
     * Get the value of a command option.
     *
     * @param  string|null  $key
     * @param  string|null  $default
     */
    public function option(string $key, string $default = null): ?string
    {
        if ($this->hasOption($key)) {
            return $this->input->getOption($key);
        }

        return $default;
    }

    /**
     * Get all of the options passed to the command.
     * @return mixed[]
     */
    public function options(): array
    {
        return $this->input->getOptions();
    }
}
