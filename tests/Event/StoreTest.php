<?php namespace C4tech\Test\RayEmitter\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Event\Store as EventStore;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Illuminate\Support\Facades\Event as EventBus;
use Mockery;

class StoreTest extends Base
{
    public function testEnqueue()
    {
        $event = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event[getId,serialize]');
        $identity = 13;
        $payload = 'test-payload';

        $event->shouldReceive('getId')
            ->withNoArgs()
            ->once()
            ->andReturn($identity);

        $event->shouldReceive('serialize')
            ->withNoArgs()
            ->once()
            ->andReturn($payload);

        EventBus::shouldReceive('fire')
            ->with($event)
            ->once();

        $store = new EventStore;
        expect_not($store->enqueue($event));
        $queue = $this->getPropertyValue($store, 'queue');
        expect($queue[0])->equals([
            'event' => get_class($event),
            'identifier' => $identity,
            'payload' => $payload
        ]);
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

        $subject->shouldReceive('newQuery->where->get')
            ->withNoArgs()
            ->with('identifier', '=', $identity)
            ->withNoArgs()
            ->once()
            ->andReturn([$record]);

        $collection = $subject->getFor($identity);
        expect($collection->first())->equals($event);
        expect($collection->count())->equals(1);
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
}
