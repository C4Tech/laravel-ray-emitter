<?php namespace C4tech\RayEmitter\Contracts\Domain;

interface Repository
{
    /**
     * Get
     *
     * Return a read-only Entity.
     * @param  string $identifier Entity identifier.
     * @return Entity
     */
    public static function get($identifier);

    /**
     * Handle
     *
     * Point of entry for Commands to allow fetching of Aggregate state.
     * @param  Command $command Command to be handled.
     * @return string           Entity identifier.
     */
    public static function handle(Command $command);
}
