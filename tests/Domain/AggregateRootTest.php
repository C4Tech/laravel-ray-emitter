<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Entity as EntityInterface;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class AggregateRootTest extends Base
{
    protected $subject;
    protected $subject_identity;

    public function setUp()
    {
        $this->subject_identity = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\ValueObject');
    }

    public function testMakeEntity()
    {
        $subject = new AggregateRootStub($this->subject_identity);
        expect($subject->getId())->equals($this->subject_identity);

        $entity = $subject->makeEntity();
        expect(get_class($entity))->equals(EntityStubSet::class);
        expect($entity->getId())->equals($this->subject_identity);
    }

    public function testSetter()
    {
        $property = 'something_fun';
        $value = false;

        $subject = Mockery::mock(AggregateRootStub::class . '[set]', [$this->subject_identity])
            ->shouldAllowMockingProtectedMethods();
        $subject->shouldReceive('set')
            ->with($property, $value)
            ->once();

        $subject->$property = $value;
    }
}
