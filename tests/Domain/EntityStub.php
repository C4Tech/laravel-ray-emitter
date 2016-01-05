<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\ValueObject;
use C4tech\RayEmitter\Domain\Entity;
use Codeception\Verify;

class EntityStub extends Entity
{
    protected $test_field;

    protected $magic_field = 'whoa';

    public function __construct(ValueObject $identifier)
    {
        parent::__construct($identifier);
        $this->test_field = 'no-way';
    }

    public function setMagicField($value)
    {
        expect($value);
        $this->magic_field = $value;
    }

    public function getMagicField()
    {
        expect(true);
        return $this->magic_field;
    }
}
