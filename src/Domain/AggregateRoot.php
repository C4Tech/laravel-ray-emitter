<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\AggregateRoot as AggregateRootInterface;
use C4tech\RayEmitter\Exceptions\UnknownProperty;

trait AggregateRoot // extends AggregateRootInterface
{
    /**
     * @inheritDoc
     */
    public function makeEntity()
    {
        $class = get_parent_class($this);
        $entity = new $class($this->getId());

        $properties = get_object_vars($this);
        foreach ($properties as $key => $value) {
            if ($key === 'identity') {
                continue;
            }

            $entity->set($key, $value);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function __set($property, $value)
    {
        $this->set($property, $value);
    }
}
