<?php

namespace Myerscode\Acorn\Foundation\Console\Display;

use Override;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DisplayOutput extends SymfonyStyle implements DisplayOutputInterface
{
    protected array $verbosityMap
        = [
            'v' => OutputInterface::VERBOSITY_VERBOSE,
            'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
            'vvv' => OutputInterface::VERBOSITY_DEBUG,
            'quiet' => OutputInterface::VERBOSITY_QUIET,
            'normal' => OutputInterface::VERBOSITY_NORMAL,
        ];

    /**
     * Formats a caution admonition.
     */
    #[Override]
    public function caution(array|string $message): void
    {
        parent::caution($message);
    }

    /**
     * Formats a command comment.
     */
    #[Override]
    public function comment(array|string $message): void
    {
        parent::comment($message);
    }

    /**
     * Write a very verbose message that is only output when the -vvv is present
     */
    public function debug(string|array $message): void
    {
        $this->writeln($this->decorateLines($message, ':orange_circle: '), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * Formats an error result bar.
     */
    #[Override]
    public function error(array|string $message): void
    {
        parent::error($message);
    }

    #[Override]
    public function info(string|array $message): void
    {
        $this->block($this->decorateLines($message, ':blue_circle: '), null, 'fg=blue', ' ', true);
    }

    /**
     * Returns whether verbosity is debug (-vvv).
     */
    #[Override]
    public function isDebug(): bool
    {
        return parent::isDebug();
    }

    /**
     * Returns whether verbosity is quiet (-q).
     */
    #[Override]
    public function isQuiet(): bool
    {
        return parent::isQuiet();
    }

    /**
     * Returns whether verbosity is verbose (-v).
     */
    #[Override]
    public function isVerbose(): bool
    {
        return parent::isVerbose();
    }

    /**
     * Returns whether verbosity is very verbose (-vv).
     */
    #[Override]
    public function isVeryVerbose(): bool
    {
        return parent::isVeryVerbose();
    }

    public function line(string|array $string, ?string $style = null, int|string|null $verbosity = null): void
    {
        $styled = $style ? sprintf('<%s>%s</%s>', $style, $string, $style) : $string;

        $this->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Formats a note admonition.
     */
    #[Override]
    public function note(string|array $message): void
    {
        parent::note($message);
    }

    #[Override]
    public function success(string|array $message): void
    {
        $this->block($this->decorateLines($message, ':green_circle: '), null, 'fg=green', ' ', false);
    }

    #[Override]
    public function table(array $headers = [], array $rows = []): void
    {
        $table = new Table($this);

        $table->setHeaders($headers)->setRows($rows);
    }

    /**
     * Formats informational text.
     */
    #[Override]
    public function text(array|string $message): void
    {
        parent::text($message);
    }

    /**
     * Write a verbose message that is only output when the -v is present
     */
    public function verbose(string|array $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_VERBOSE);
    }

    /**
     * Gets the current verbosity of the output.
     */
    public function verbosity(): int
    {
        return $this->getVerbosity();
    }

    /**
     * Write a very verbose message that is only output when the -vv is present
     */
    public function veryVerbose(string|array $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    /**
     * Formats an warning result bar.
     */
    #[Override]
    public function warning(array|string $message): void
    {
        parent::warning($message);
    }

    protected function decorateLines(string|array $messages, string $prefix = '', string $postfix = ''): array
    {
        $messages = is_array($messages) ? array_values($messages) : [$messages];

        return array_map(fn ($message): string => $this->encodeLine($prefix . $message . $postfix), $messages);
    }

    protected function encodeLine(string $messages): string
    {
        return $messages;
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     */
    protected function parseVerbosity(string|int|null $level = null): int
    {
        if (isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (!is_int($level)) {
            $level = $this->verbosityMap['normal'];
        }

        return $level;
    }
}
