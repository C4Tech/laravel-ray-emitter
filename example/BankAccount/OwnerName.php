<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\ValueObject;

class OwnerName extends ValueObject
{
    protected $name = '';

    /**
     * Constructor
     * @param string $name Owner's name.
     */
    public function __construct($name)
    {
        $this->name = strval($name);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->name;
    }
}
