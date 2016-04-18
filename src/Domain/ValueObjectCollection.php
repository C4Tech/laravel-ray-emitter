<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\ValueObject;
use Illuminate\Support\Collection;

abstract class ValueObjectCollection extends Collection implements ValueObject
{
    /**
     * Collection Class
     *
     * Class name of member Value Objects
     * @var string
     */
    protected $collectionClass = '';

    /**
     * @inheritDoc
     */
    public function equals(ValueObject $other)
    {
        if (get_class($other) !== get_class($this)) {
            return false;
        }

        if ($other->getHash() === $this->getHash()) {
            return true;
        }

        return false;
    }

    /**
     * Get Hash
     *
     * Calculate an md5 sum of the collection.
     * @return string
     */
    public function getHash()
    {
        return hash('md5', json_encode($this));
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->all();
    }

    /**
     * @inheritDoc
     */
    public static function jsonSerialize()
    {
        return [
            'class' => $this->collectionClass,
            'content' => parent::jsonSerialize()
        ];
    }

    /**
     * @inheritDoc
     */
    public static function jsonUnserialize($data)
    {
        $class = $data['class'];

        $values = array_map(
            function ($value) use ($class) {
                return $class::jsonUnserialize($value);
            },
            $data['content']
        );

        return new static($values);
    }

    /**
     * Sort By Value
     *
     * Sort the array by the VO's value.
     * @return static
     */
    public function sortByValue()
    {
        return $this->sortBy(function ($item) {
            return $item->getValue();
        });
    }
}
