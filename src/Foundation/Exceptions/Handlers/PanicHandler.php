<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Foundation\Exceptions\Handlers;

use Myerscode\Acorn\Framework\Exceptions\ErrorHandler;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

/**
 * Handles AppPanicException and other critical failures.
 *
 * Outputs a clear panic message and suggests running with -v for details.
 */
class PanicHandler extends ErrorHandler
{
    public function handle(Throwable $exception, OutputInterface $output): ?bool
    {
        $output->writeln('');
        $output->writeln('  <error> PANIC </error>');
        $output->writeln('');
        $output->writeln(sprintf('  <fg=red>%s</>', $exception->getMessage() ?: 'The application encountered a fatal error.'));
        $output->writeln('');

        if (!$output->isVerbose()) {
            $output->writeln('  <comment>Run with -v for more details.</comment>');
            $output->writeln('');
        } else {
            $output->writeln(sprintf('  <comment>%s:%d</comment>', $exception->getFile(), $exception->getLine()));
            $output->writeln('');
        }

        return true;
    }
}
