<?php namespace C4tech\RayEmitter\Contracts\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;

interface Store
{
    /**
     * Save Event
     *
     * Add an event into the Event Store.
     * @param  EventInterface $event The event to add.
     * @return void
     */
    public function saveEvent(EventInterface $event);

    /**
     * Get For
     *
     * Retrieve and rehydrate all events related to an Entity identifier.
     * @param  string $identifier Entity identity.
     * @return C4tech\RayEmitter\Contracts\Event\Collection
     */
    public function getFor($identifier);
}
