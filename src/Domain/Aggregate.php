<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Aggregate as AggregateInterface;
use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\RayEmitter\Contracts\Domain\Event as EventInterface;
use C4tech\RayEmitter\Contracts\Event\Collection as EventCollectionInterface;
use C4tech\RayEmitter\Exceptions\CommandHandlerMissing;
use C4tech\RayEmitter\Exceptions\EventHandlerMissing;
use Illuminate\Support\Facades\Config;

abstract class Aggregate implements AggregateInterface
{
    /**
     * Aggregate root entity.
     * @var AggregateRootInterface
     */
    protected $root;

    /**
     * Event sequence counter.
     * @var integer
     */
    protected $sequence = -1;

    /**
     * @inheritDoc
     */
    public function apply(EventInterface $event)
    {
        $method = $this->createMethodName('apply', $event);

        if (!method_exists($this, $method)) {
            throw new EventHandlerMissing(
                sprintf(
                    'Command %s does not have a handler for its expected aggregate %s',
                    get_class($event),
                    get_class($this)
                ),
                501
            );
        }

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
    protected function createMethodName($prefix_key, $object, $prefix_default = null)
    {
        if (!$prefix_default) {
            $prefix_default = $prefix_key;
            $prefix_key .= '_prefix';
        }

        $prefix = Config::get('ray_emitter.' . $prefix_key, $prefix_default);
        $base = class_basename($object);

        return $prefix . $base;
    }

    /**
     * @inheritDoc
     */
    public function getEntity()
    {
        return $this->root->makeEntity();
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->root->getId();
    }

    /**
     * @inheritDoc
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @inheritDoc
     */
    public function handle(CommandInterface $command)
    {
        $method = $this->createMethodName('handle', $command);

        if (!method_exists($this, $method)) {
            throw new CommandHandlerMissing(
                sprintf(
                    'Command %s does not have a handler for its expected aggregate %s',
                    get_class($command),
                    get_class($this)
                ),
                501
            );
        }

        return $this->$method($command);
    }

    /**
     * @inheritDoc
     */
    public function hydrate(EventCollectionInterface $events)
    {
        $events->each(function (EventInterface $event) {
            $this->apply($event);
            $this->sequence++;
        });
    }

    /**
     * Magic Getter
     *
     * Expose getter methods on the Aggregate and Aggregate Root as properties.
     * @param  string $property Requested "property"
     * @return mixed
     */
    public function __get($property)
    {
        $method = 'get' . studly_case($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->root->$property;
    }
}
