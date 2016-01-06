<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Entity as EntityInterface;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class EntityTest extends Base
{
    protected $subject;
    protected $subject_identity;

    public function setUp()
    {
        $this->subject_identity = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\ValueObject');
        $this->subject = Mockery::mock(EntityStub::class, [$this->subject_identity])
            ->makePartial();
    }

    public function testConstructor()
    {

        $identity = $this->getPropertyValue($this->subject, 'identity');

        expect($identity)->equals($this->subject_identity);
    }

    public function testConstructorMethod()
    {
        $subject = new EntityStubSet($this->subject_identity);
        expect($subject->test_value)->equals($this->subject_identity);
    }

    public function testGetId()
    {
        expect($this->subject->getId())->equals($this->subject_identity);
    }

    public function testSetCallsMethod()
    {
        $value = 'amazing';

        $set = $this->getMethod($this->subject, 'set');
        expect_not($set->invoke($this->subject, 'magic_field', $value));

        expect($this->subject->getMagicField())->equals($value);
    }

    public function testSetAccessesProperty()
    {
        $set = $this->getMethod($this->subject, 'set');
        expect_not($set->invoke($this->subject, 'test_field', false));
        $value = $this->getPropertyValue($this->subject, 'test_field');
        expect($value)->equals(false);
    }

    /**
     * @expectedException C4tech\RayEmitter\Exceptions\UnknownProperty
     */
    public function testSetThrowsError()
    {
        $set = $this->getMethod($this->subject, 'set');
        expect_not($set->invoke($this->subject, 'other_field', false));
    }

    public function testGetterCallsMethod()
    {
        expect($this->subject->magic_field)->equals($this->subject->getMagicField());
    }

    public function testGetterPullsProperty()
    {
        expect($this->subject->test_field)->equals('no-way');
    }

    /**
     * @expectedException C4tech\RayEmitter\Exceptions\UnknownProperty
     */
    public function testGetterThrowsError()
    {
        expect_not($this->subject->other_field);
    }
}
