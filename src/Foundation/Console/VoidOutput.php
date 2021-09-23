<?php

namespace Myerscode\Acorn\Foundation\Console;

use Myerscode\Acorn\Framework\Console\ConsoleOutputInterface;
use Symfony\Component\Console\Output\NullOutput;

class VoidOutput extends NullOutput implements ConsoleOutputInterface
{

}
