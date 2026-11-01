<?php

declare(strict_types=1);

namespace Tests\Resources\App\Commands;

use Myerscode\Acorn\Framework\Console\Command;
use Override;
use Tests\Resources\CorgiService;

/**
 * A command with a constructor dependency, autowired by the container when it
 * is built during discovery.
 */
final class ServiceBackedCommand extends Command
{
    #[Override]
    protected string $description = 'Uses an injected service.';

    #[Override]
    protected string $signature = 'service:backed';

    public function __construct(private readonly CorgiService $service)
    {
        parent::__construct();
    }

    #[Override]
    public function handle(): int
    {
        $this->output->writeln($this->service->name());

        return 0;
    }
}
