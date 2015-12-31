<?php namespace C4tech\RayEmitter\Contracts\Domain;

interface Repository
{
    /**
     * Create
     *
     * Generate a new Aggregate with no history.
     * @return Aggregate
     */
    public static function create();

    /**
     * Find
     *
     * Restore an existing Aggregate from the recorded events related to it.
     * @param  string $identifier Aggregate root entity identifier.
     * @return Aggregate
     */
    public static function find($identifier);

    /**
     * Handle
     *
     * Point of entry for Commands to allow fetching of Aggregate state.
     * @param  Command $command Command to be handled.
     * @return void
     */
    public static function handle(Command $command);

    /**
     * Save
     *
     * Push new events into the Event Store.
     * @param  Aggregate $aggregate Aggregate to save.
     * @return void
     */
    public static function save(Aggregate $aggregate);
}
