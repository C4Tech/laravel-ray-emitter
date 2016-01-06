<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Entity as EntityInterface;
use C4tech\RayEmitter\Contracts\Domain\ValueObject as ValueObjectInterface;
use C4tech\RayEmitter\Exceptions\UnknownProperty;

abstract class Entity implements EntityInterface
{
    /**
     * The Entity's identity.
     * @var ValueObjectInterface
     */
    protected $identity;

    /**
     * @inheritDoc
     */
    public function __construct(ValueObjectInterface $identifier)
    {
        if (method_exists($this, 'setId')) {
            $this->setId($identifier);
        } else {
            $this->identity = $identifier;
        }
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->identity;
    }

    /**
     * Set
     *
     * Protected method to set property value.
     * @param  string $property Requested "property"
     * @param  mixed  $value    Requested value
     * @return mixed
     */
    protected function set($property, $value)
    {
        $method = 'set' . studly_case($property);

        if ($property !== 'id' && method_exists($this, $method)) {
            $this->$method($value);
        } elseif ($property !== 'identity' && property_exists($this, $property)) {
            $this->$property = $value;
        } else {
            throw new UnknownProperty('There is no way to set ' . $property, 504);
        }

    }

    /**
     * Magic Getter
     *
     * Expose getter methods as properties.
     * @param  string $property Requested "property"
     * @return mixed
     */
    public function __get($property)
    {
        $method = 'get' . studly_case($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        } elseif (isset($this->$property)) {
            return $this->$property;
        }

        throw new UnknownProperty('Cannot find the property ' . $property . ' on ' . get_class($this), 504);
    }
}
