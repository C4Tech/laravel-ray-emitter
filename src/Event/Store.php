<?php namespace C4tech\RayEmitter\Event;

use Illuminate\Support\Facades\Event as EventBus;

class Store
{
    private $queue = [];

    public static function enqueue(EventInterface $event)
    {
        self::$queue[] = [
            'eventable_type' => get_class($event),
            'eventable_id'   => $event->getId(),
            'payload'        => $event->serialize()
        ];

        EventBus::fire($event);
    }

    public static function getFor($identifier)
    {
        $events = new Collection;

        $recorded_events = self::where('eventable_id', '=', $identifier)->get();
        foreach ($recorded_events as $record) {
            $events->append(self::restoreEvent($record));
        }

        return $events;
    }

    private static function restoreEvent($record)
    {
        $class = $record->type;

        return $class::restore($record);
    }
}
