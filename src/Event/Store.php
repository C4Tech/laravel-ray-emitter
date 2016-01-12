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
    protected $fillable = [
        'identifier',
        'sequence',
        'event',
        'payload'
    ];

    public function enqueue(EventInterface $event)
    {
        static::$queue[] = [
            'event'      => get_class($event),
            'identifier' => $event->getId(),
            'payload'    => $event->serialize()
        ];

        EventBus::fire($event);
    }

    public function getFor($identifier)
    {
        $events = new Collection;

        $recorded_events = $this->newQuery()->forEntity($identifier)->get();
        foreach ($recorded_events as $record) {
            $events->append($this->restoreEvent($record));
        }

        return $events;
    }

    protected function restoreEvent($record)
    {
        $class = $record->event;

        return $class::unserialize($record);
    }

    public function saveQueue()
    {
        foreach (static::$queue as $record) {
            $record['sequence'] = $this->newQuery()->forEntity($record['identifier'])
                ->count();
            static::create($record);
        }

        static::$queue = [];
    }

    public function scopeForEntity($query, $identifier)
    {
        return $query->where('identifier', '=', $identifier);
    }
}
