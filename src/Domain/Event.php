<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;

abstract class Event implements EventInterface
{
    /**
     * Global identifier for the entity related to this event.
     * @var string
     */
    protected $identifier;

    /**
     * Payload of event alterations.
     * @var object
     */
    protected $payload;

    /**
     * Version sequence number.
     * @var integer
     */
    protected $sequence = 0;

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

    public static function restore($data)
    {
        $event = static::unserialize($data->id, $data->payload);
        $event->setSequence($data->sequence);
        $event->setTimestamp($data->recorded_on);

        return $event;
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
    public static function unserialize($identifier, $json)
    {
        $payload = json_decode($json);
        $data = [];

        foreach ($payload as $key => $config) {
            $class = $config['class'];
            $data[$key] = $class::jsonUnserialize($config['value']);
        }

        return new static($identifier, $data);
    }
}
