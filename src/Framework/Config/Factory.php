<?php

namespace Myerscode\Acorn\Framework\Config;

use Myerscode\Config\Config;

class Factory
{

    static function make(array $data): Config
    {
        $config = new Config();

        $config->loadData($data);

        $config->loadFilesWithNamespace([
            __DIR__ . '/../../Config/App.php',
            __DIR__ . '/../../Config/Executing.php',
            __DIR__ . '/../../Config/Framework.php',
        ]);

        return $config;
    }
}
