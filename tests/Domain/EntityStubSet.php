<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Domain\Entity;
use Codeception\Verify;

class EntityStubSet extends Entity
{
    public $test_value;

    public function setId($value)
    {
        expect($value);
        $this->identity = $value;
        $this->test_value = $value;
    }

    public function setMagic($value)
    {
        expect($value);
    }

    public function setTestValue($value)
    {
        expect($value);
    }
}
