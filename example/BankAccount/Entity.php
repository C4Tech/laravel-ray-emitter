<?php namespace C4tech\RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\Entity as AbstractEntity;

class Entity extends AbstractEntity
{
    /**
     * Owner name.
     * @var OwnerName
     */
    protected $owner;

    /**
     * Account balance.
     * @var UsDollar
     */
    protected $balance;
}
