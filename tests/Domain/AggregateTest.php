<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\Support\Test\Base;
use Codeception\Verify;
use Illuminate\Support\Facades\Config;
use Mockery;
use Mockery\MockInterface;

class AggregateTest extends Base
{
    /**
     * @expectedException C4tech\RayEmitter\Exceptions\EventHandlerMissing
     */
    public function testApplyThrowsError()
    {
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');

        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub[createMethodName]')
            ->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('createMethodName')
            ->with('apply', $event)
            ->once()
            ->andReturn('applyFalse');

        $subject->shouldReceive('applyTrue')
            ->never()
            ->with($event);

        $apply = $this->getMethod($subject, 'apply');
        expect_not($apply->invoke($subject, $event));
    }

    public function testApplyCallsMethod()
    {
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');

        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub[createMethodName]')
            ->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('createMethodName')
            ->with('apply', $event)
            ->once()
            ->andReturn('applyTrue');

        $apply = $this->getMethod($subject, 'apply');
        expect_not($apply->invoke($subject, $event));
    }

    public function testCreateMethodNameMakesDefault()
    {
        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub');
        $key = 'blast';
        $object = new AggregateStub;

        Config::shouldReceive('get')
            ->with('ray_emitter.blast_prefix', 'blast')
            ->once()
            ->andReturn('blast');

        $create = $this->getMethod($subject, 'createMethodName');
        expect($create->invoke($subject, $key, $object))
            ->equals('blastAggregateStub');
    }

    public function testCreateMethodNameDoesDefault()
    {
        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub');
        $key = 'blast_level';
        $default = 'shrink';
        $object = new AggregateStub;

        Config::shouldReceive('get')
            ->with('ray_emitter.blast_level', 'shrink')
            ->once()
            ->andReturn('shrink');

        $create = $this->getMethod($subject, 'createMethodName');
        expect($create->invoke($subject, $key, $object, $default))
            ->equals('shrinkAggregateStub');
    }

    public function testGetEntityCallsRoot()
    {
        $value = false;

        $identity = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\ValueObject');
        $root = Mockery::mock(AggregateRootStub::class . '[makeEntity]', [$identity]);
        $root->shouldReceive('makeEntity')
            ->withNoArgs()
            ->once()
            ->andReturn($value);

        $subject = new AggregateStub;
        $this->setPropertyValue($subject, 'root', $root);

        expect($subject->getEntity())->equals($value);
    }

    public function testGetIdCallsRoot()
    {
        $value = false;

        $identity = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\ValueObject');
        $root = Mockery::mock(AggregateRootStub::class . '[getId]', [$identity]);
        $root->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($value);

        $subject = new AggregateStub;
        $this->setPropertyValue($subject, 'root', $root);

        expect($subject->getId())->equals($value);
    }

    public function testGetSequence()
    {
        $value = 4;

        $subject = new AggregateStub;
        $this->setPropertyValue($subject, 'sequence', $value);

        expect($subject->getSequence())->equals($value);
    }

    /**
     * @expectedException C4tech\RayEmitter\Exceptions\CommandHandlerMissing
     */
    public function testHandleThrowsError()
    {
        $command = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Command');

        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub[createMethodName]')
            ->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('createMethodName')
            ->with('handle', $command)
            ->once()
            ->andReturn('handleFalse');

        $subject->shouldReceive('handleTrue')
            ->never()
            ->with($command);

        $handle = $this->getMethod($subject, 'handle');
        expect_not($handle->invoke($subject, $command));
    }

    public function testHandleCallsMethod()
    {
        $command = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Command');

        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub[createMethodName]')
            ->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('createMethodName')
            ->with('handle', $command)
            ->once()
            ->andReturn('handleTrue');

        $handle = $this->getMethod($subject, 'handle');
        $event = $handle->invoke($subject, $command);
        expect($event instanceof MockInterface)->equals(true);
    }

    public function testHydrateLoops()
    {
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');

        $collection = Mockery::mock('C4tech\RayEmitter\Contracts\Event\Collection[each]');

        $collection->shouldReceive('each')
            ->with(Mockery::on(function ($callback) use ($event) {
                $callback($event);

                return true;
            }))
            ->once();

        $subject = Mockery::mock('C4tech\Test\RayEmitter\Domain\AggregateStub[apply]')
            ->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('apply')
            ->with($event)
            ->once();

        $original_sequence = $subject->getSequence();

        expect_not($subject->hydrate($collection));

        expect($subject->getSequence())->greaterThan($original_sequence);
    }

    public function testGetterMethod()
    {
        $value = 4;

        $subject = new AggregateStub;
        $this->setPropertyValue($subject, 'sequence', $value);

        $sequence = $subject->getSequence();

        expect($subject->sequence)->equals($sequence);
        expect($subject->sequence)->equals($value);
    }

    public function testGetterRoot()
    {
        $subject = new AggregateStub;
        $value = 'testMagic';

        $root = Mockery::mock(AggregateRootStub::class);
        $this->setPropertyValue($subject, 'root', $root);

        expect($subject->magic)->equals($value);
    }
}
