<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\Repository as AbstractRepository;

class Repository extends AbstractRepository
{
    public static function create()
    {
        return new Aggregate;
    }
}
