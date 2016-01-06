<?php namespace C4tech\RayEmitter\Contracts\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;

interface Collection
{
    /**
     * Append
     *
     * Append a new element to the collection.
     * @param  EventInterface $event
     * @return void
     */
    public function append(EventInterface $event);

    /**
     * Each
     *
     * Execute a callback on each element in the collection.
     * @param  callable $callback The function to execute
     * @return void
     */
    public function each(callable $callback);
}
