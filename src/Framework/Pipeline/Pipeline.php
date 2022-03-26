<?php

namespace Myerscode\Acorn\Framework\Pipeline;

use Closure;
use Myerscode\Acorn\Framework\Exceptions\InvalidPipeException;

class Pipeline
{

    /**
     * The collection of pipes that a value will be passed through
     *
     * @var PipeInterface[]
     */
    private array $pipes;


    public function __construct(array|string|PipeCollectionInterface $pipes = [])
    {
        if (is_string($pipes)) {
            $pipes = [$pipes];
        }

        $this->setPipes($pipes);
    }

    /**
     * Set the pipes that will be passed through
     *
     * @param  mixed  $pipes
     */
    public function pipes($pipes): static
    {
        if ($pipes instanceof Pipeline) {
            $pipes = $pipes->toArray();
        } elseif (!is_array($pipes) && is_subclass_of($pipes, PipeInterface::class)) {
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
     * @param  Closure|null  $core
     *
     * @return mixed
     */
    public function flush(mixed $object, ?Closure $core = null): mixed
    {

        if (is_null($core)) {
            $core = fn($object) => $object;
        }

        $coreFunction = fn($object) => $core($object);

        // Since we will be "currying" the functions starting with the first
        // in the array, the first function will be "closer" to the core.
        // This also means it will be run last. However, if the reverse the
        // order of the array, the first in the list will be the outer pipes.
        $pipes = array_reverse($this->pipes);

        // We create the onion by starting initially with the core and then
        // gradually wrap it in pipes. Each layer will have the next layer "curried"
        // into it and will have the current state (the object) passed to it.
        $completeOnion = array_reduce($pipes, fn($nextLayer, $layer): Closure => $this->createLayer($nextLayer, $layer), $coreFunction);

        // We now have the complete onion and can start passing the object
        // down through the pipes.
        return $completeOnion($object);
    }

    public function setPipes($pipes): void
    {
        $this->pipes = array_filter($pipes, fn($pipe): bool => $pipe instanceof PipeCollectionInterface || is_subclass_of($pipe, PipeInterface::class));
    }

    /**
     * Get the pipes of this onion, can be used to merge with another onion
     *
     * @return PipeInterface[]
     */
    public function toArray(): array
    {
        return $this->pipes;
    }

    /**
     * Get an onion layer function.
     * This function will get the object from a previous layer and pass it inwards
     *
     * @param  Closure  $nextLayer
     * @param  PipeCollectionInterface|PipeInterface|string  $pipe
     *
     * @return callable
     */
    private function createLayer(Closure $nextLayer, PipeCollectionInterface|PipeInterface|string $pipe): callable
    {
        if ($pipe instanceof PipeCollectionInterface) {
            return fn($object) => $nextLayer( (new Pipeline($pipe->toArray()))->flush($object) );
        }

        if (is_string($pipe)) {
            $pipe = new $pipe();
        }

        return fn($object) => $pipe->handle($object, $nextLayer);
    }

}
