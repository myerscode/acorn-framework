<?php

namespace Myerscode\Acorn\Framework\Pipeline;

class LineManager
{
    /**
     * Thing that will be passed through the pipelines
     */
    private mixed $object;

    /**
     * @var PipeCollection[]
     */
    private array $pipes = [];

    public function send(mixed $object): static
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @param string[] $pipes
     */
    public function setPipeline(string $name, array $pipes): void
    {
        $this->pipes[$name] = new PipeCollection($pipes);
    }

    /**
     * Run the object through one or more named pipelines.
     *
     * When a single string is passed, the pipes from that pipeline are flattened
     * and run directly. When an array of names is passed, each named pipeline
     * runs as a nested pipeline in sequence (first pipeline completes before second starts).
     *
     * @param string|string[] $pipeNames A single pipeline name or an array of names to run in sequence
     */
    public function through(string|array $pipeNames): mixed
    {
        if (is_string($pipeNames) && isset($this->pipes[$pipeNames])) {
            $pipes = $this->pipes[$pipeNames]->toArray();
        } else {
            $pipeNamesArray = is_array($pipeNames) ? $pipeNames : [$pipeNames];
            $pipes = array_values(array_filter(
                $this->pipes,
                fn (PipeCollection $collection, string $name): bool => in_array($name, $pipeNamesArray),
                ARRAY_FILTER_USE_BOTH,
            ));
        }

        return new Pipeline($pipes)->flush($this->object);
    }
}
