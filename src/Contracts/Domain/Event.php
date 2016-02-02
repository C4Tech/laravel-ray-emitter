<?php namespace C4tech\RayEmitter\Contracts\Domain;

use stdClass;

interface Event
{
    /**
     * Get ID
     *
     * Access the globally unique identifier for the related Entity/Aggregate.
     * @return string
     */
    public function getId();

    /**
     * Get Payload
     *
     * Return the data payload for the Event.
     * @return mixed
     */
    public function getPayload();

    /**
     * Serialize
     *
     * Reduce payload data to a JSON string for storage.
     * @return string JSON
     */
    public function serialize();

    /**
     * Unserialize
     *
     * Restore payload data from a stored JSON string.
     * @param  stdClass $record Stored Event record
     * @return static
     */
    public static function unserialize($record);
}
