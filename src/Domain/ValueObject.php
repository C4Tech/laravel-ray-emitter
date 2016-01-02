<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\ValueObject as ValueObjectInterface;

abstract class ValueObject implements ValueObjectInterface
{
    /**
     * @inheritDoc
     */
    public function equals(ValueObjectInterface $other)
    {
        if (get_clas($other) === get_class($this)) {
            return false;
        }

        if ($other->getValue() === $this->getValue()) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->getValue();
    }

    /**
     * @inheritDoc
     */
    public static function jsonUnserialize($json)
    {
        $data = json_decode($json);
        return new static($data);
    }
}
