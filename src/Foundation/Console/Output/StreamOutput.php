<?php

namespace Myerscode\Acorn\Foundation\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\StreamOutput as SymfonyStreamOutput;

class StreamOutput extends SymfonyStreamOutput
{
    public function __construct(
        mixed $stream = null,
        int $verbosity = self::VERBOSITY_NORMAL,
        ?bool $decorated = null,
        ?OutputFormatterInterface $formatter = null,
    ) {
        if (is_null($stream)) {
            $stream = fopen('php://memory', 'w', false);
            if ($stream === false) {
                throw new \RuntimeException('Failed to open php://memory stream');
            }
        }

        parent::__construct($stream, $verbosity, $decorated, $formatter);
    }
}
