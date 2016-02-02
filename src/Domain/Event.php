<?php namespace C4tech\RayEmitter\Domain;

use stdClass;
use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;

abstract class Event implements EventInterface
{
    /**
     * Global identifier for the entity related to this event.
     * @var string
     */
    protected $identifier;

    /**
     * Version sequence number.
     * @var integer
     */
    protected $sequence = 0;

    /**
     * Date object of when the event occurred.
     * @var DateTime
     */
    protected $timestamp;

    /**
     * Payload of event alterations.
     * @var object
     */
    protected $payload;

    public function __construct($identifier, array $payload)
    {
        $this->identifier = $identifier;

        $this->payload = new stdClass;
        foreach ($payload as $key => $value) {
            $this->payload->$key = $value;
        }
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->identifier;
    }

    /**
     * @inheritDoc
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @inheritDoc
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @inheritDoc
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        $payload = [];
        foreach (get_object_vars($this->payload) as $key => $value_object) {
            $payload[$key] = [
                'class' => get_class($value_object),
                'value' => json_encode($value_object)
            ];
        }

        return json_encode($payload);
    }

    /**
     * @inheritDoc
     */
    public static function unserialize($record)
    {
        $payload = json_decode($record->payload, true);
        $data = [];

        foreach ($payload as $key => $config) {
            $class = $config['class'];
            $data[$key] = $class::jsonUnserialize($config['value']);
        }

        $event = new static($record->identifier, $data);
        $event->sequence = $record->sequence;
        $event->timestamp = $record->created_at;

        return $event;
    }
}
