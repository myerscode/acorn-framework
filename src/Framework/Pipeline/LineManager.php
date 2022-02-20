<?php

namespace Myerscode\Acorn\Framework\Pipeline;

class LineManager
{
    private $object;

    /**
     * @var PipeInterface[]|Pipeline
     */
    private $pipes;

    /**
     * @var array
     */
    private array $pipelines;


    public function setPipeline($name, $pipes)
    {
        $this->pipelines[$name] = function ($object) use ($pipes) {
            return (new Pipeline)->pipes($pipes)->flush($object);
        };
    }

    public function send($object): LineManager
    {
        $this->object = $object;

        return $this;
    }

    public function through($pipes)
    {
        if (is_string($pipes) && isset($this->pipelines[$pipes])) {
            return $this->pipelines[$pipes]($this->object);
        }

        return (new Pipeline($this->pipes))->flush($this->object);
    }
}
