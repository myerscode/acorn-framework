<?php

declare(strict_types=1);

namespace Myerscode\Acorn\Framework\Exceptions;

use Symfony\Component\Console\Exception\CommandNotFoundException as SymfonyCommandNotFoundException;

class CommandNotFoundException extends SymfonyCommandNotFoundException
{
}
