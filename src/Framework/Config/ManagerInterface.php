<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\Config\Config;

interface ManagerInterface
{
    public function loadConfig(array $configLocations, array $data = []): Config;
}
