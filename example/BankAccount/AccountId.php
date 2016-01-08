<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\ValueObject;

class AccountId extends ValueObject
{
    protected $uuid = '';

    /**
     * Constructor
     * @param string $uuid UUID.
     */
    public function __construct($uuid)
    {
        if (strlen($uuid) < 32 || strlen($uuid) > 36) {
            throw new InvalidIdValue;
        }

        $this->uuid = strval($uuid);
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->uuid;
    }
}
