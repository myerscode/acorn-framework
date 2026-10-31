<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Console;

/**
 * Graceful signal handling for long-running commands.
 *
 * `use` this trait AND implement Symfony's SignalableCommandInterface on your
 * command, then register handlers in handle() for a clean shutdown on Ctrl-C
 * (SIGINT) or SIGTERM:
 *
 *   public function handle(): int
 *   {
 *       $this->onSignal(SIGINT, function (): void {
 *           $this->output->warning('Stopping...');
 *       });
 *       // ... long-running work ...
 *   }
 *
 * A registered signal defaults to terminating with exit code 0 after the
 * handler runs; return an int from a handler to use a different exit code, or
 * false to keep running. Requires the pcntl extension (handlers simply never
 * fire without it).
 */
trait InteractsWithSignals
{
    /** @var array<int, list<callable(int): (int|false|null)>> */
    private array $signalHandlers = [];

    /**
     * Signals this command listens for (the SignalableCommandInterface
     * contract). Derived from whatever has been registered via onSignal().
     *
     * @return int[]
     */
    public function getSubscribedSignals(): array
    {
        return array_keys($this->signalHandlers);
    }

    /**
     * Dispatch a received signal to the registered handlers.
     *
     * The last non-null value a handler returns wins: an int becomes the exit
     * code (terminating), false keeps the command running. With no explicit
     * return the command terminates cleanly with exit code 0.
     */
    public function handleSignal(int $signal, int|false $previousExitCode = 0): int|false
    {
        $result = 0;

        foreach ($this->signalHandlers[$signal] ?? [] as $handler) {
            $returned = $handler($signal);

            if ($returned !== null) {
                $result = $returned;
            }
        }

        return $result;
    }

    /**
     * Register a handler for one or more POSIX signals.
     *
     * @param int|int[] $signals
     * @param callable(int): (int|false|null) $handler
     */
    public function onSignal(int|array $signals, callable $handler): void
    {
        foreach ((array) $signals as $signal) {
            $this->signalHandlers[$signal][] = $handler;
        }
    }
}
