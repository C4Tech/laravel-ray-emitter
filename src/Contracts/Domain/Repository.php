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
     * Get Entity
     *
     * Return the Aggregate Root as a read-only Entity.
     * @param  string $identifier Aggregate root entity identifier.
     * @return Entity
     */
    public static function getEntity($identifier);

    /**
     * Handle
     *
     * Point of entry for Commands to allow fetching of Aggregate state.
     * @param  Command $command Command to be handled.
     * @return void
     */
    public static function handle(Command $command);
}
