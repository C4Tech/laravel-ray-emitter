<?php namespace C4tech\RayEmitter\Contracts\Domain;

use JsonSerializable;

interface ValueObject extends JsonSerializable
{
    /**
     * Equals
     *
     * Determine if a Value Object is equal to this one.
     * @param  ValueObjectInterface $other The other Value Object for comparison.
     * @return boolean
     */
    public function equals(ValueObject $other);

    /**
     * Get Value
     *
     * Retrieve the value of the Value Object.
     * @return mixed
     */
    public function getValue();

    /**
     * JSON Unserialize
     *
     * Recreate the Value Object from its serialized JSON data.
     * @param  string $json The stored JSON data string from static::jsonSerialize().
     * @return static
     */
    public static function jsonUnserialize($json);
}
