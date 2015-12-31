<?php namespace C4tech\RayEmitter\Domain;

use stdClass;
use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;

abstract class Command implements CommandInterface
{
    /**
     * Target aggregate root entity identifier.
     * @var string
     */
    protected $target_id;

    /**
     * Expected sequence version of the target aggregate
     * @var int
     */
    protected $expected_sequence = -1;

    /**
     * Command data payload.
     * @var stdClass
     */
    protected $payload;

    /**
     * @inheritDoc
     */
    public function getAggregateId()
    {
        return $this->target_id;
    }

    /**
     * @inheritDoc
     */
    public function getExpectedSequence()
    {
        return $this->expected_sequence;
    }

    /**
     * @inheritDoc
     * @return stdClass
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Setup Payload
     *
     * Initializes the payload as an empty object.
     * @return void
     */
    protected function setupPayload()
    {
        $this->payload = new stdClass;
    }
}
