<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Exceptions\Handlers;

use Myerscode\Acorn\Framework\Exceptions\ErrorHandler;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * The fallback handler for any exception that doesn't have a specific handler mapped.
 *
 * Renders a formatted error message. Users can replace this by setting
 * 'default' in their Error config.
 */
class DefaultHandler extends ErrorHandler
{
    public function handle(Throwable $exception, OutputInterface $output): ?bool
    {
        $output->writeln('');
        $output->writeln(sprintf('  <error> %s </error>', $this->shortName($exception)));
        $output->writeln('');
        $output->writeln(sprintf('  <fg=red>%s</>', $exception->getMessage() ?: $exception::class));

        if ($output->isVerbose()) {
            $output->writeln('');
            $output->writeln(sprintf('  <comment>%s:%d</comment>', $exception->getFile(), $exception->getLine()));
        }

        if ($output->isVeryVerbose()) {
            $output->writeln('');
            $output->writeln('  <comment>Exception trace:</comment>');

            foreach (array_slice($exception->getTrace(), 0, 10) as $i => $frame) {
                $file = $frame['file'] ?? 'unknown';
                $line = $frame['line'] ?? 0;
                $class = $frame['class'] ?? '';
                $function = $frame['function'] ?? '';
                $type = $frame['type'] ?? '';

                $call = $class !== '' ? $class . $type . $function . '()' : $function . '()';
                $output->writeln(sprintf('  <fg=gray>%d. %s</>', $i + 1, $call));
                $output->writeln(sprintf('     <fg=gray>%s:%d</>', $file, $line));
            }
        }

        $output->writeln('');

        return true;
    }

    private function shortName(Throwable $exception): string
    {
        $class = $exception::class;
        $parts = explode('\\', $class);

        return end($parts);
    }
}
