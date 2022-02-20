<?php

namespace Myerscode\Acorn\Framework\Pipeline;

use Closure;
use InvalidArgumentException;
use Myerscode\Acorn\Framework\Exceptions\InvalidPipeException;

class Pipeline
{

    private $originalObject;

    /**
     * The collection of pipes that a value will be passed through
     *
     * @var PipeInterface[]
     */
    private array $pipes;


    public function __construct(array $pipes = [])
    {
        $this->pipes = array_filter($pipes, function ($pipe) {
            return ($pipe instanceof PipeInterface || is_subclass_of($pipe, PipeInterface::class));
        });
    }

    /**
     * Set the pipes that will be passed through
     *
     * @param  mixed  $pipes
     *
     * @return Pipeline
     */
    public function pipes($pipes): Pipeline
    {
        if ($pipes instanceof Pipeline) {
            $pipes = $pipes->toArray();
        }

        if ($pipes instanceof PipeInterface) {
            $pipes = [$pipes];
        }

        if (!is_array($pipes)) {
            throw new InvalidPipeException($pipes . " is not a valid pipe.");
        }

        return new static(array_merge($this->pipes, $pipes));
    }

    /**
     * Run middleware around core function and pass an
     * object through it
     *
     * @param  mixed  $object
     * @param  Closure  $core
     *
     * @return mixed
     */
    public function flush($object, ?Closure $core = null)
    {

        if (is_null($core)) {
            $core = function ($object) {
                return $object;
            };
        }

        $coreFunction = function ($object) use ($core) {
            return $core($object);
        };

        // Since we will be "currying" the functions starting with the first
        // in the array, the first function will be "closer" to the core.
        // This also means it will be run last. However, if the reverse the
        // order of the array, the first in the list will be the outer pipes.
        $pipes = array_reverse($this->pipes);

        // We create the onion by starting initially with the core and then
        // gradually wrap it in pipes. Each layer will have the next layer "curried"
        // into it and will have the current state (the object) passed to it.
        $completeOnion = array_reduce($pipes, function ($nextLayer, $layer) {
            return $this->createLayer($nextLayer, $layer);
        }, $coreFunction);

        // We now have the complete onion and can start passing the object
        // down through the pipes.
        return $completeOnion($object);
    }

    /**
     * Get the pipes of this onion, can be used to merge with another onion
     *
     * @return PipeInterface[]
     */
    public function toArray()
    {
        return $this->pipes;
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards
     *
     * @param  PipeInterface  $nextLayer
     * @param  PipeInterface  $layer
     *
     * @return Closure
     */
    private function createLayer($nextLayer, $layer)
    {
        if (is_string($layer) && is_subclass_of($layer, PipeInterface::class)) {
            $layer = new $layer();
        }

        return function ($object) use ($nextLayer, $layer) {
            return $layer->handle($object, $nextLayer);
        };
    }

}
