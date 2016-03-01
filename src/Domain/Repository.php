<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\RayEmitter\Contracts\Domain\Repository as RepositoryInterface;
use C4tech\RayEmitter\Facades\EventStore;
use C4tech\RayEmitter\Exceptions\OutdatedSequence;
use C4tech\RayEmitter\Exceptions\SequenceMismatch;

abstract class Repository implements RepositoryInterface
{
    /**
     * @inheritDoc
     */
    public static function get($identifier)
    {
        $aggregate = static::find($identifier);

        return $aggregate->getEntity();
    }

    /**
     * @inheritDoc
     */
    public static function handle(CommandInterface $command)
    {
        $aggregate = static::find($command->getAggregateId());

        // Optimistic concurrency handling
        $expected = $command->getExpectedSequence();
        $current = $aggregate->getSequence();
        if ($expected < $current) {
            throw new OutdatedSequence(
                sprintf(
                    'The Aggregate %s has newer data than expected (%s v %s).',
                    get_class($aggregate),
                    $expected,
                    $current
                ),
                409
            );
        } elseif ($expected > $current) {
            throw new SequenceMismatch(
                sprintf(
                    'The Aggregate %s is expected to have more data than it does (%s v %s).',
                    get_class($aggregate),
                    $expected,
                    $current
                ),
                422
            );
        }

        $identifier = null;

        // Queue event for storage
        if ($event = $aggregate->handle($command)) {
            $aggregate->apply($event);
            EventStore::saveEvent($event);
            $identifier = $event->getId();
        }

        return $identifier;
    }

    /**
     * Create
     *
     * Generate a new Aggregate with no history.
     * @return Aggregate
     */
    protected static function create()
    {
        throw new Exception('The ' . static::class . '::create method must be defined');
    }

    /**
     * Find
     *
     * Restore an existing Aggregate from the recorded events related to it.
     * @param  void|string $identifier Aggregate root entity identifier.
     * @return Aggregate
     */
    protected static function &find($identifier = null)
    {
        $aggregate = static::create();

        if ($identifier) {
            $events = EventStore::getFor($identifier);
            $aggregate->hydrate($events);
        }

        return $aggregate;
    }
}
