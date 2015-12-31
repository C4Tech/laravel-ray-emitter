<?php namespace C4tech\RayEmitter\Contracts\Domain;

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
     * @param string $json JSON-encoded data
     * @return void
     */
    public static function unserialize($json);
}
