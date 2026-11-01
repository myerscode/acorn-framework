<?php

namespace Myerscode\Acorn\Foundation\Console\Display;

use Override;
use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Yaml\Yaml;

class DisplayOutput extends SymfonyStyle implements DisplayOutputInterface
{
    /**
     * Emoji shortcodes translated for terminal output. Extend as needed.
     *
     * @var array<string, string>
     */
    private const array EMOJI_SHORTCODES = [
        ':blue_circle:' => '🔵',
        ':orange_circle:' => '🟠',
        ':green_circle:' => '🟢',
        ':red_circle:' => '🔴',
        ':white_check_mark:' => '✅',
        ':x:' => '❌',
        ':warning:' => '⚠️',
        ':information_source:' => 'ℹ️',
    ];

    /** @var array<string, int> */
    protected array $verbosityMap = [
        'v' => OutputInterface::VERBOSITY_VERBOSE,
        'vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        'vvv' => OutputInterface::VERBOSITY_DEBUG,
        'quiet' => OutputInterface::VERBOSITY_QUIET,
        'normal' => OutputInterface::VERBOSITY_NORMAL,
    ];

    /**
     * Formats a caution admonition.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function caution(array|string $message): void
    {
        parent::caution($message);
    }

    /**
     * Formats a command comment.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function comment(array|string $message): void
    {
        parent::comment($message);
    }

    /**
     * Write rows as CSV. Each row is an array of scalar cells; an optional
     * header row is written first. Useful for `--format=csv` output that pipes
     * into spreadsheets or other tooling.
     *
     * @param array<int, array<int|string, scalar|null>> $rows
     * @param array<int, string> $headers
     */
    #[Override]
    public function csv(array $rows, array $headers = []): void
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return;
        }

        if ($headers !== []) {
            fputcsv($handle, $headers, escape: '');
        }

        foreach ($rows as $row) {
            fputcsv($handle, array_map(static fn ($cell): string => (string) $cell, array_values($row)), escape: '');
        }

        rewind($handle);
        $this->writeln(rtrim((string) stream_get_contents($handle)), OutputInterface::OUTPUT_RAW);
        fclose($handle);
    }

    /**
     * Write a very verbose message that is only output when the -vvv is present
     *
     * @param string|string[] $message
     */
    public function debug(string|array $message): void
    {
        $this->writeln($this->decorateLines($message, ':orange_circle: '), OutputInterface::VERBOSITY_DEBUG);
    }

    /**
     * Formats an error result bar.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function error(array|string $message): void
    {
        parent::error($message);
    }

    /**
     * @param string|string[] $message
     */
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

    /**
     * Write data as JSON, for commands that support machine-readable output
     * (e.g. a `--format=json` flag). Pretty-printed by default; pass
     * $pretty=false for compact single-line output suitable for piping.
     */
    #[Override]
    public function json(mixed $data, bool $pretty = true): void
    {
        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | ($pretty ? JSON_PRETTY_PRINT : 0);

        // Write the raw JSON with no decoration so the output is valid to pipe
        // into another tool; bypass the styled writeln formatting.
        $this->writeln((string) json_encode($data, $flags), OutputInterface::OUTPUT_RAW);
    }

    /**
     * @param string|string[] $string
     */
    public function line(string|array $string, ?string $style = null, int|string|null $verbosity = null): void
    {
        if (is_array($string)) {
            foreach ($string as $line) {
                $this->line($line, $style, $verbosity);
            }

            return;
        }

        $styled = $style ? sprintf('<%s>%s</%s>', $style, $string, $style) : $string;

        $this->writeln($styled, $this->parseVerbosity($verbosity));
    }

    /**
     * Formats a note admonition.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function note(string|array $message): void
    {
        parent::note($message);
    }

    /**
     * @param string|string[] $message
     */
    #[Override]
    public function success(string|array $message): void
    {
        $this->block($this->decorateLines($message, ':green_circle: '), null, 'fg=green', ' ', false);
    }

    /**
     * @param array<mixed> $headers
     * @param array<mixed> $rows
     */
    #[Override]
    public function table(array $headers = [], array $rows = []): void
    {
        $table = new Table($this);

        $table->setHeaders($headers)->setRows($rows)->render();
    }

    /**
     * Formats informational text.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function text(array|string $message): void
    {
        parent::text($message);
    }

    /**
     * Render a title bar above subsequent output.
     *
     * @param string $message
     */
    #[Override]
    public function title(string $message): void
    {
        parent::title($message);
    }

    /**
     * Write a verbose message that is only output when the -v is present
     *
     * @param string|string[] $message
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
     *
     * @param string|string[] $message
     */
    public function veryVerbose(string|array $message): void
    {
        $this->writeln($message, OutputInterface::VERBOSITY_VERY_VERBOSE);
    }

    /**
     * Formats an warning result bar.
     *
     * @param string|string[] $message
     */
    #[Override]
    public function warning(array|string $message): void
    {
        parent::warning($message);
    }

    /**
     * Write data as YAML, for commands that support `--format=yaml` output.
     */
    #[Override]
    public function yaml(mixed $data, int $inline = 4): void
    {
        $this->writeln(rtrim(Yaml::dump($data, $inline, 2)), OutputInterface::OUTPUT_RAW);
    }

    /**
     * @param string|string[] $messages
     * @return string[]
     */
    protected function decorateLines(string|array $messages, string $prefix = '', string $postfix = ''): array
    {
        $messages = is_array($messages) ? array_values($messages) : [$messages];

        return array_map(fn (string $message): string => $this->encodeLine($prefix . $message . $postfix), $messages);
    }

    /**
     * Translate emoji shortcodes (e.g. :blue_circle:) into their Unicode
     * characters so they render in the terminal instead of printing the raw
     * `:name:` text.
     */
    protected function encodeLine(string $messages): string
    {
        return strtr($messages, self::EMOJI_SHORTCODES);
    }

    /**
     * Get the verbosity level in terms of Symfony's OutputInterface level.
     *
     * @return int<0, 511>
     */
    protected function parseVerbosity(string|int|null $level = null): int
    {
        if (is_string($level) && isset($this->verbosityMap[$level])) {
            $level = $this->verbosityMap[$level];
        } elseif (!is_int($level)) {
            $level = $this->verbosityMap['normal'];
        }

        // Keep within Symfony's valid output-flag range (int<0, 511>).
        return max(0, min(511, $level));
    }
}
