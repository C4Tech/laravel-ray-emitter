<?php namespace C4tech\RayEmitter\Facades;

use Illuminate\Support\Facades\Facade;

class EventStore extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor()
    {
        return 'rayemitter.store';
    }
}
