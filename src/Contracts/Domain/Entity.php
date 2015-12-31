<?php namespace C4tech\RayEmitter\Contracts\Domain;

interface Entity
{
    /**
     * Constructor
     *
     * Instantiate an Entity using its identifier.
     * @param  ValueObjectInterface $identifier The identity Value Object.
     * @return static
     */
    public function __construct(ValueObject $identifier);
}
