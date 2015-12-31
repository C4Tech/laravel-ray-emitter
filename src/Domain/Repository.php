<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Aggregate as AggregateInterface;
use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\RayEmitter\Contracts\Domain\Repository as RepositoryInterface;

class Repository implements RepositoryInterface
{
    /**
     * @inheritDoc
     */
    public static function find($identifier)
    {
        $aggregate = static::create();
        $events = EventLog::getFor($identifier);
        $aggregate->hydrate($events);
    }

    /**
     * @inheritDoc
     */
    public static function handle(CommandInterface $command)
    {
        if ($aggregate_id = $command->getAggregateId()) {
            $instance = static::restore($aggregate_id);
        } else {
            $instance = static::create();
        }

        $expected_sequence = $command->getExpectedSequence();
        $current_sequence = $instance->getSequence();
        if ($expected_sequence < $current_sequence) {
            throw new OutdatedSequenceException();
        } elseif ($expected_sequence > $current_sequence) {
            throw new SequenceMismatchException();
        }

        $instance->handle($command);
    }

    /**
     * @inheritDoc
     */
    public static function save(AggregateInterface $aggregate)
    {
        EventStore::append($aggregate->flush());
    }
}
