<?php namespace C4tech\RayEmitter\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Contracts\Event\Store as StoreInterface;
use Illuminate\Support\Facades\Event as EventBus;
use Illuminate\Database\Eloquent\Model;

class Store extends Model implements StoreInterface
{
    protected static $queue = [];

    /**
     * @inheritDoc
     */
    protected $table = 'event_store';

    /**
     * @inheritDoc
     */
    protected $fillable = [
        'identifier',
        'sequence',
        'event',
        'payload'
    ];

    /**
     * @inheritDoc
     */
    public function enqueue(EventInterface $event)
    {
        static::$queue[] = [
            'event'      => get_class($event),
            'identifier' => $event->getId(),
            'payload'    => $event->serialize()
        ];

        EventBus::fire($event);
    }

    /**
     * @inheritDoc
     */
    public function getFor($identifier)
    {
        $events = new Collection;

        $recorded_events = $this->newQuery()->forEntity($identifier)->get();
        foreach ($recorded_events as $record) {
            $events->append($this->restoreEvent($record));
        }

        return $events;
    }

    /**
     * Restore Event
     *
     * Unserialize saved record back into an Event.
     * @param  static         $record Event Store model record
     * @return EventInterface
     */
    protected function restoreEvent($record)
    {
        $class = $record->event;

        return $class::unserialize($record);
    }

    /**
     * Save Queue
     *
     * Persist all queued Events into Event Store.
     * @return void
     */
    public function saveQueue()
    {
        foreach (static::$queue as $record) {
            $record['sequence'] = $this->newQuery()->forEntity($record['identifier'])
                ->count();
            static::create($record);
        }

        static::$queue = [];
    }

    /**
     * Scope: For Entity
     *
     * Query scope for Entity identifier.
     * @param  Query  $query      Query Builder
     * @param  string $identifier Entity identifier
     * @return Query
     */
    public function scopeForEntity($query, $identifier)
    {
        return $query->where('identifier', '=', $identifier);
    }
}
