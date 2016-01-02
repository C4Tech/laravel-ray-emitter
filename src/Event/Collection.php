<?php namespace C4tech\RayEmitter\Event;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Contracts\Event\Collection as CollectionInterface;
use Illuminate\Support\Collection as BaseCollection;

class Collection extends BaseCollection implements CollectionInterface
{
    /**
     * @inheritDoc
     */
    public function append(EventInterface $event)
    {
        $this->push($event);
    }
}
