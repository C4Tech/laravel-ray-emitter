<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Aggregate as AggregateInterface;
use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Contracts\Event\Collection as EventCollectionInterface;
use C4tech\RayEmitter\Event\Collection as EventCollection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event as EventBus;

abstract class Aggregate implements AggregateInterface
{
    /**
     * Aggregate data
     * @var array
     */
    protected $data = [];

    /**
     * Queue of uncommitted Events.
     * @var EventCollection
     */
    private $event_queue;

    /**
     * Aggregate root entity identifier.
     * @var ValueObjectInterface
     */
    private $identifier;

    /**
     * Aggregate root entity.
     * @var EntityInterface
     */
    protected $root;

    /**
     * Event sequence counter.
     * @var integer
     */
    private $sequence = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->event_queue = new EventCollection;
    }

    /**
     * Apply
     *
     * Alter the Aggregate Root's state by adding a single event.
     * @param  EventInterface $event An event that has occurred.
     * @return void
     */
    private function apply(EventInterface $event)
    {
        $method = $this->createMethodName('apply', $event);

        if (!method_exists($this, $method)) {
            throw new MethodMissingException();
        }

        $event->checkSequence($this->sequence++);
        $this->$method($event);
    }

    /**
     * Create Method Name
     *
     * Generate a method name using a configurable prefix and an object's class basename.
     * @param  string $prefix_key     The key from the configuration to lookup the appropriate prefix.
     * @param  object $object         The object which shall be reduced to its class basename.
     * @param  string $prefix_default The default prefix.
     * @return string                 The method name to use.
     */
    private function createMethodName($prefix_key, $object, $prefix_default = null)
    {
        if (!$prefix_default) {
            $prefix_default = $prefix_key;
        }

        $prefix = Config::get('ray_emitter.' . $prefix_key . '_prefix', $prefix_default);
        $base = basename(get_class($object));

        return $prefix . $base;
    }

    /**
     * @inheritDoc
     */
    public function flush()
    {
        return $this->event_queue->flush();
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
    public function handle(CommandInterface $command)
    {
        $method = $this->createMethodName('handle', $command);

        if (!method_exists($this, $method)) {
            throw new CommandHandlerMissingException();
        }

        $event = $this->$method($command);
        $this->publish($event);
    }

    /**
     * @inheritDoc
     */
    public function hydrate(EventCollectionInterface $events)
    {
        $events->each(function ($event) {
            $this->apply($event);
        });
    }

    /**
     * Publish
     *
     * Adds a new event to the event queue, applies it to to the Aggregate Root's
     * state, and broadcasts the Event for listeners to handle.
     * @param  EventInterface $event An Event created by a Command handler.
     * @return void
     */
    private function publish(EventInterface $event)
    {
        $this->event_queue->append($event);
        $this->apply($event);

        EventBus::fire($event);
    }
}
