<?php namespace C4tech\Test\RayEmitter\Domain;

use DateTime;
use C4tech\RayEmitter\Facades\EventStore;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class RepositoryTest extends Base
{
    protected $identifier = 'abc-123';

    protected $sequence = 30;

    public function setUp()
    {
        $this->subject = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Aggregate')
            ->makePartial();
        RepositoryStub::$mock = &$this->subject;
    }

    public function tearDown()
    {
        parent::tearDown();
        EventStore::clearResolvedInstances();
        $this->setPropertyValue(new RepositoryStub, 'aggregates', [], true);
    }

    protected function coverRestore()
    {
        $collection = Mockery::mock('C4tech\RayEmitter\Contracts\Event\Collection');

        EventStore::shouldReceive('getFor')
            ->with($this->identifier)
            ->once()
            ->andReturn($collection);

        $this->subject->shouldReceive('hydrate')
            ->with($collection)
            ->once();
    }

    protected function makeCommand()
    {
        $command = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Command')
            ->makePartial();

        $command->shouldReceive('getExpectedSequence')
            ->withNoArgs()
            ->andReturn($this->sequence);

        $command->shouldReceive('getAggregateId')
            ->withNoArgs()
            ->andReturn($this->identifier);

        $this->coverRestore();

        return $command;
    }

    public function testFind()
    {
        $this->coverRestore();

        $method = $this->getMethod(new RepositoryStub, 'find');
        expect($method->invoke(null, $this->identifier))->equals($this->subject);
    }

    public function testGet()
    {
        $value = 'magic';

        $this->coverRestore();

        $this->subject->shouldReceive('getEntity')
            ->withNoArgs()
            ->once()
            ->andReturn($value);

        expect(RepositoryStub::get($this->identifier))->equals($value);
    }

    /**
     * @expectedException C4tech\RayEmitter\Exceptions\SequenceMismatch
     */
    public function testHandleCatchesOutdatedCommands()
    {
        $command = $this->makeCommand();

        $this->subject->shouldReceive('getSequence')
            ->withNoArgs()
            ->once()
            ->andReturn(12);

        expect_not(RepositoryStub::handle($command));
    }

    /**
     * @expectedException C4tech\RayEmitter\Exceptions\OutdatedSequence
     */
    public function testHandleCatchesPostdatedCommands()
    {
        $command = $this->makeCommand();

        $this->subject->shouldReceive('getSequence')
            ->withNoArgs()
            ->once()
            ->andReturn(42);

        expect_not(RepositoryStub::handle($command));
    }

    public function testHandlePassesToAggregate()
    {
        $command = $this->makeCommand();

        $this->subject->shouldReceive('getSequence')
            ->withNoArgs()
            ->once()
            ->andReturn(30);

        $this->subject->shouldReceive('handle')
            ->with($command)
            ->once();


        expect_not(RepositoryStub::handle($command));
    }


    public function testHandlePushesNewEvent()
    {
        $command = $this->makeCommand();
        $identity = rand();
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');
        $event->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($identity);

        $this->subject->shouldReceive('getSequence')
            ->withNoArgs()
            ->once()
            ->andReturn(30);

        $this->subject->shouldReceive('handle')
            ->with($command)
            ->once()
            ->andReturn($event);

        $this->subject->shouldReceive('apply')
            ->with($event)
            ->once();

        EventStore::shouldReceive('saveEvent')
            ->with($event)
            ->once();

        expect(RepositoryStub::handle($command))->equals($identity);
    }
}
