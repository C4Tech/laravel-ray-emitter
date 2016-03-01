<?php namespace C4tech\Test\RayEmitter\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Event\Collection;
use C4tech\RayEmitter\Event\Store as EventStore;
use C4tech\Support\Test\Model;
use Codeception\Verify;
use Illuminate\Support\Facades\Event as EventBus;
use Mockery;

class StoreTest extends Model
{
    public function tearDown()
    {
        parent::tearDown();
        EventBus::clearResolvedInstances();
    }

    public function testGetFor()
    {
        $record = 'saved-record';
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');
        $identity = '123-abc';

        $subject = Mockery::mock(EventStore::class)->makePartial();
        $subject->shouldAllowMockingProtectedMethods();

        $subject->shouldReceive('restoreEvent')
            ->with($record)
            ->once()
            ->andReturn($event);

        $subject->shouldReceive('newQuery->forEntity->get')
            ->withNoArgs()
            ->with($identity)
            ->withNoArgs()
            ->once()
            ->andReturn([$record]);

        $collection = $subject->getFor($identity);
        expect($collection->first())->equals($event);
        expect($collection->count())->equals(1);
    }

    public function testReplayAll()
    {
        $record = new \stdClass;
        $record->event = 'EventName';

        $collection = new Collection([$record]);

        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');

        $store = Mockery::mock(EventStore::class)
            ->makePartial();
        $store->shouldAllowMockingProtectedMethods();

        $store->shouldReceive('all')
            ->withNoArgs()
            ->once()
            ->andReturn($collection);

        $store->shouldReceive('restoreEvent')
            ->with($record)
            ->once()
            ->andReturn($event);

        EventBus::shouldReceive('fire')
            ->with('replay:' . $record->event, [$event])
            ->once();

        expect_not($store->replayAll());
    }

    public function testRestoreEvent()
    {
        $subject = new EventStore;
        $record = Mockery::mock('stdClass');
        $record->event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event[unserialize]');
        $record->event->shouldReceive('unserialize')
            ->with($record)
            ->once()
            ->andReturn($record);

        $method = $this->getMethod($subject, 'restoreEvent');
        expect($method->invoke($subject, $record))->equals($record);
    }

    public function testSaveEvent()
    {
        $store = Mockery::mock(EventStore::class)
            ->makePartial();
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event[getId,serialize]');
        $identity = 13;
        $payload = 'test-payload';
        $sequence = 3;
        $expected_event = [
            'event' => get_class($event),
            'identifier' => $identity,
            'payload' => $payload,
            'sequence' => $sequence
        ];
        $expected_record = [
            'event' => get_class($event),
            'payload' => [$event]
        ];

        $event->shouldReceive('getId')
            ->withNoArgs()
            ->twice()
            ->andReturn($identity);

        $event->shouldReceive('serialize')
            ->withNoArgs()
            ->once()
            ->andReturn($payload);

        EventBus::shouldReceive('fire')
            ->with('save:' . get_class($event), [$event])
            ->once();

        $store->shouldReceive('newQuery->forEntity->count')
            ->withNoArgs()
            ->with($identity)
            ->withNoArgs()
            ->once()
            ->andReturn($sequence);

        $store->shouldReceive('create')
            ->with($expected_event)
            ->once();

        expect_not($store->saveEvent($event));
        $queue = $this->getPropertyValue($store, 'queue');
        expect($queue[0])->equals($expected_record);
    }

    public function testPublishQueue()
    {
        $store = Mockery::mock(EventStore::class)
            ->makePartial();
        $payload = [Mockery::mock('stdClass')];
        $event_name = 'testEvent';
        $event = [
            'event' => $event_name,
            'payload' => $payload
        ];

        EventBus::shouldReceive('fire')
            ->with('publish:' . $event_name, $payload)
            ->once();

        $this->setPropertyValue($store, 'queue', [$event]);

        expect_not($store->publishQueue());

        $queue = $this->getPropertyValue($store, 'queue');
        expect($queue)->equals([]);
    }

    public function testScopeForEntity()
    {
        $identity = 12;
        $this->setModel(EventStore::class);
        $this->verifyScopeWhere('scopeForEntity', 'identifier', $identity);
    }
}
