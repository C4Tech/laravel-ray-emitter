<?php namespace C4tech\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\ValueObject as ValueObjectInterface;
use Illuminate\Support\Collection;

abstract class ValueObjectCollection extends ValueObject
{
    /**
     * Collection Class
     *
     * Class name of member Value Objects
     * @var string
     */
    protected $collectionClass = '';

    public function __construct(array $values = [])
    {
        $this->values = new Collection($values);
        $this->sort();
    }

    /**
     * @inheritDoc
     */
    public function equals(ValueObjectInterface $other)
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
        return hash('md5', json_encode($this->values));
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->values->all();
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'class' => $this->collectionClass,
            'values' => $this->values->jsonSerialize()
        ];
    }

    /**
     * @inheritDoc
     */
    public static function jsonUnserialize($json)
    {
        $data = json_decode($json, true);
        $class = $data['class'];

        $values = array_map(
            function ($value) use ($class) {
                return new $class($value);
            },
            $data['values']
        );

        return new static($values);
    }

    public function push(ValueObject $value)
    {
        $this->values->push($value);
    }

    /**
     * Sort By Value
     *
     * Sort the array by the VO's value.
     * @return void
     */
    private function sort()
    {
        $this->values = $this->values->sortBy(function ($item) {
            return $item->getValue();
        });
    }
}
