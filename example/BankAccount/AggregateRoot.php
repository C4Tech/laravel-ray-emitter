<?php namespace C4tech\RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Contracts\Domain\AggregateRoot as AggregateRootInterface;
use C4tech\RayEmitter\Domain\AggregateRoot as RootTrait;

final class AggregateRoot extends Entity implements AggregateRootInterface
{
    use RootTrait;

    /**
     * Account balance
     * @var UsDollar
     */
    private $balance;

    /**
     * Owner name
     * @var OwnerName
     */
    private $owner;

    protected function setBalance(UsDollar $value)
    {
        $this->balance = $value;
    }

    protected function setOwner(OwnerName $value)
    {
        $this->owner = $value;
    }
}
