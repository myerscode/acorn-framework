<?php

namespace Myerscode\Acorn\Framework\Helpers;

use Myerscode\Utilities\Files\Utility;

class FileService
{
    public function using(string $path): Utility
    {
        return new Utility($path);
    }
}
