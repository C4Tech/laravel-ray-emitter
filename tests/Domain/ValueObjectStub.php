<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Domain\ValueObject;

class ValueObjectStub extends ValueObject
{
    public $data;

    public function __construct($data)
    {
        $this->data = (array) $data;
    }

    public function getValue()
    {
        return $this->data;
    }
}
