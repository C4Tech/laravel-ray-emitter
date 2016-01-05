<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Domain\Repository;
use Mockery;

class RepositoryStub extends Repository
{
    public static $mock;
    public static function create()
    {
        return static::$mock;
    }
}
