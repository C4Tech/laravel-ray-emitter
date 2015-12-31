<?php namespace C4tech\RayEmitter\Contracts\Domain;

interface Command
{
    /**
     * Get Aggregate Id
     *
     * Return the aggregate's root entity identifier
     * @return string
     */
    public function getAggregateId();

    /**
     * Get Expected Sequence
     *
     * Return the expected sequence version for the target aggregate.
     * @return int
     */
    public function getExpectedSequence();

    /**
     * Get Payload
     *
     * Return the data payload for the Command.
     * @return mixed
     */
    public function getPayload();

    /**
     * Run
     *
     * Executes state change request against an Aggregate.
     * @return void
     */
    public function run();
}
