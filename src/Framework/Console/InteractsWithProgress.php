<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console;

use Myerscode\Acorn\Framework\Console\Display\DisplayOutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

trait InteractsWithProgress
{
    protected DisplayOutputInterface $output;

    /**
     * Create a new progress bar instance.
     */
    public function createProgressBar(int $max = 0): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $max);
        $progressBar->setFormat('normal');

        return $progressBar;
    }

    /**
     * Execute a callback while displaying a progress bar.
     *
     * The callback receives each item and the progress bar instance.
     *
     * @template TKey of array-key
     * @template TValue
     * @param iterable<TKey, TValue> $items
     * @param callable(TValue, ProgressBar): void $callback
     */
    public function withProgressBar(iterable $items, callable $callback): void
    {
        $items = is_array($items) ? $items : iterator_to_array($items);
        $progressBar = $this->createProgressBar(count($items));
        $progressBar->start();

        foreach ($items as $item) {
            $callback($item, $progressBar);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->output->writeln('');
    }

    /**
     * Execute a callback while displaying an indeterminate progress indicator (spinner).
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function withSpinner(callable $callback, string $message = 'Processing...'): mixed
    {
        $this->output->write($message);
        $result = $callback();
        $this->output->writeln(' <info>done.</info>');

        return $result;
    }

    /**
     * Execute a long-running task with a status message, showing elapsed time on completion.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public function withTask(string $description, callable $callback): mixed
    {
        $this->output->write($description);
        $startTime = microtime(true);

        $result = $callback();

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->output->writeln(sprintf(' <info>(%ss)</info>', $elapsed));

        return $result;
    }
}
