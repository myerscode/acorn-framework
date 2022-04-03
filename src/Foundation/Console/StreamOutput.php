<?php

namespace Myerscode\Acorn\Foundation\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StreamOutput extends Output
{
    public function __construct(InputInterface $input, OutputInterface $output, protected mixed $stream = null)
    {
        if (is_null($stream)) {
            $this->stream = fopen('php://memory', 'w', false);
        }

        parent::__construct($input, $output);
    }

    /**
     * Gets the stream attached to this StreamOutput instance.
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }

    public function writeln(string|iterable $messages, int $type = self::OUTPUT_NORMAL)
    {
        if (!is_iterable($messages)) {
            $messages = [$messages];
        }

        foreach ($messages as $message) {
            $this->writeToStream($message, true);
        }
    }

    protected function writeToStream(string $message, bool $newline)
    {
        if ($newline) {
            $message .= \PHP_EOL;
        }

        @fwrite($this->stream, $message);

        fflush($this->stream);
    }
}
