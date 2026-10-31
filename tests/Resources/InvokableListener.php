<?php

declare(strict_types=1);

namespace Tests\Resources;

use Myerscode\Acorn\Framework\Events\EventInterface;
use Myerscode\Acorn\Framework\Events\Listener;
use Override;

/**
 * A listener that defines __invoke() instead of overriding handle(), to prove
 * the base Listener bridges invokable listeners through handle().
 */
class InvokableListener extends Listener
{
    public bool $invoked = false;

    public function __invoke(EventInterface $event): void
    {
        $this->invoked = true;
    }

    #[Override]
    public function listensFor(): array|string
    {
        return TestEvent::class;
    }
}
