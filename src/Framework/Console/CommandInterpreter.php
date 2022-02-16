<?php

namespace Myerscode\Acorn\Framework\Console;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use function Myerscode\Acorn\Foundation\text;

/**
 * Based on laravel/framework Illuminate\Console\Parser
 */
class CommandInterpreter
{
    /**
     * Parse the given console command definition into an array.
     *
     * @param  string  $expression
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function parse($expression)
    {
        $name = $this->name($expression);

        if (preg_match_all('#\{\s*(.*?)\s*\}#', $expression, $matches) && count($matches[1])) {
            return array_merge([$name], $this->parameters($matches[1]));
        }

        return [$name, [], []];
    }

    /**
     * Extract the name of the command from the expression.
     *
     * @param  string  $expression
     *
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function name($expression)
    {
        if (!preg_match('#[^\s]+#', $expression, $matches)) {
            throw new InvalidArgumentException('Unable to determine command name from signature.');
        }

        return $matches[0];
    }

    /**
     * Extract all of the parameters from the tokens.
     */
    protected function parameters(array $tokens): array
    {
        $arguments = [];

        $options = [];

        foreach ($tokens as $token) {
            if (preg_match('#-{2,}(.*)#', $token, $matches)) {
                $options[] = $this->parseOption($matches[1]);
            } else {
                $arguments[] = $this->parseArgument($token);
            }
        }

        return [$arguments, $options];
    }

    /**
     * Parse an argument expression.
     */
    protected function parseArgument(string $token): InputArgument
    {
        [$token, $description] = $this->extractDescription($token);
        $matches = [];

        return match (true) {
            text($token)->endsWith('?*') => new InputArgument(trim($token, '?*'), InputArgument::IS_ARRAY, $description),
            text($token)->endsWith('*') => new InputArgument(trim($token, '*'), InputArgument::IS_ARRAY | InputArgument::REQUIRED, $description),
            text($token)->endsWith('?') => new InputArgument(trim($token, '?'), InputArgument::OPTIONAL, $description),
            text($token)->endsWith(['=', '=']) => new InputArgument(trim($token, '='), InputArgument::OPTIONAL, $description, null),
            text($token)->matches('/(.+)=\*(.+)/', $matches) => new InputArgument($matches[1], InputArgument::IS_ARRAY, $description,
                preg_split('#,\s?#', $matches[2])),
            text($token)->matches('/(.+)=(.+)/', $matches) => new InputArgument($matches[1], InputArgument::OPTIONAL, $description, $matches[2]),
            default => new InputArgument($token, InputArgument::REQUIRED, $description),
        };
    }

    /**
     * Parse an option expression.
     */
    protected function parseOption(string $token): InputOption
    {
        [$token, $description] = $this->extractDescription($token);

        $matches = preg_split('#\s*\|\s*#', $token, 2);

        if (isset($matches[1])) {
            $shortcut = $matches[0];
            $token = $matches[1];
        } else {
            $shortcut = null;
        }

        return match (true) {
            text($token)->endsWith('=') => new InputOption(trim($token, '='), $shortcut, InputOption::VALUE_OPTIONAL, $description),
            text($token)->endsWith('=*') => new InputOption(trim($token, '=*'), $shortcut, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                $description),
            text($token)->matches('/(.+)=\*(.+)/', $matches) => new InputOption($matches[1], $shortcut, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, $description, preg_split('#,\s?#', $matches[2])),
            text($token)->matches('/(.+)=(.+)/', $matches) => new InputOption($matches[1], $shortcut, InputOption::VALUE_OPTIONAL, $description, $matches[2]),
            default => new InputOption($token, $shortcut, InputOption::VALUE_NONE, $description),
        };
    }

    /**
     * Parse the token into its token and description segments.
     */
    protected function extractDescription(string $token): array
    {
        $parts = preg_split('#\s+:\s+#', trim($token), 2);

        return count($parts) === 2 ? $parts : [$token, ''];
    }
}
