<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Domain\AggregateRoot;
use C4tech\RayEmitter\Domain\Entity;

class AggregateRootStub extends EntityStubSet
{
    use AggregateRoot;

    protected $magic = 'testMagic';
}
