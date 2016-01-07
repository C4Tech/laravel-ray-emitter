<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Contracts\Domain\AggregateRoot as AggregateRootInterface;
use C4tech\RayEmitter\Domain\AggregateRoot as RootTrait;

final class AggregateRoot extends Entity implements AggregateRootInterface
{
    use RootTrait;

    protected function setBalance(UsDollar $value)
    {
        $this->balance = $value;
    }

    protected function setOwner(OwnerName $value)
    {
        $this->owner = $value;
    }
}
