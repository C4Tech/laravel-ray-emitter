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

    /**
     * Get Id
     *
     * Return the identifier Value Object.
     * @return ValueObjectInterface
     */
    public function getId();

    /**
     * Magic Getter
     *
     * Expose getter methods as properties.
     * @param  string $property Requested "property"
     * @return mixed
     */
    public function __get($property);
}
