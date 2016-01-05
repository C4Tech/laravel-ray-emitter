<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Aggregate as AggregateInterface;
use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\RayEmitter\Contracts\Domain\Repository as RepositoryInterface;
use C4tech\RayEmitter\Event\Store as EventStore;
use C4tech\RayEmitter\Exceptions\OutdatedSequence;
use C4tech\RayEmitter\Exceptions\SequenceMismatch;

abstract class Repository implements RepositoryInterface
{
    /**
     * @inheritDoc
     */
    public static function find($identifier)
    {
        $aggregate = static::create();
        static::restore($identifier, $aggregate);

        return $aggregate;
    }

    /**
     * @inheritDoc
     */
    public static function getEntity($identifier)
    {
        $aggregate = static::find($identifier);

        return $aggregate->getEntity();
    }

    /**
     * @inheritDoc
     */
    public static function handle(CommandInterface $command)
    {
        $aggregate = static::create();
        if ($aggregate_id = $command->getAggregateId()) {
            static::restore($aggregate_id, $aggregate);
        }

        // Optimistic concurrency handling
        $expected = $command->getExpectedSequence();
        $current = $aggregate->getSequence();
        if ($expected < $current) {
            throw new OutdatedSequence(
                sprintf(
                    'The Aggregate %s has newer data than expected.',
                    get_class($aggregate)
                ),
                409
            );
        } elseif ($expected > $current) {
            throw new SequenceMismatch(
                sprintf(
                    'The Aggregate %s is expected to have more data than it does',
                    get_class($aggregate)
                ),
                422
            );
        }

        $aggregate->handle($command);
    }

    /**
     * Restore
     *
     * Hydrate an aggregate with its recorded events.
     * @param  string             $identifier Aggregate root entity identifier.
     * @param  AggregateInterface &$aggregate (Fresh) Aggregate to hydrate.
     * @return void
     */
    protected static function restore($identifier, &$aggregate)
    {
        $events = EventStore::getFor($identifier);
        $aggregate->hydrate($events);
    }

    /**
     * @inheritDoc
     */
    public static function save(AggregateInterface $aggregate)
    {
        EventStore::append($aggregate->flush());
    }
}
