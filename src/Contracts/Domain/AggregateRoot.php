<?php namespace C4tech\RayEmitter\Contracts\Domain;

interface AggregateRoot extends Entity
{
    /**
     * Make Entity
     *
     * Transform this Root into a read-only Entity.
     * @return Entity
     */
    public function makeEntity();

    /**
     * Magic Setter
     *
     * Expose setter methods as properties.
     * @param  string $property Requested "property"
     * @param  mixed  $value    Requested value
     * @return mixed
     */
    public function __set($property, $value);
}
