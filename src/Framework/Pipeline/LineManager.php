<?php

namespace Myerscode\Acorn\Framework\Pipeline;

class LineManager
{
    /**
     * Thing that will be passed through the pipelines
     *
     * @var mixed
     */
    private mixed $object;

    /**
     * @var PipeCollection[]
     */
    private array $pipes = [];

    public function setPipeline($name, array $pipes): void
    {
        $this->pipes[$name] = new PipeCollection($pipes);
    }

    public function send(mixed $object): static
    {
        $this->object = $object;

        return $this;
    }

    /**
     * @param  string|array  $pipeNames
     *
     * @return mixed
     */
    public function through(string|array $pipeNames): mixed
    {
        if (is_string($pipeNames) && isset($this->pipes[$pipeNames])) {
            $pipes =  $this->pipes[$pipeNames]->toArray();
        } else {
            $pipes = array_values(array_filter($this->pipes, fn($name) => in_array($name, $pipeNames), ARRAY_FILTER_USE_KEY));
        }

        return (new Pipeline($pipes))->flush($this->object);
    }
}
