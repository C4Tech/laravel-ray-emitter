<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\ValueObject;

class UsDollar extends ValueObject
{
    protected $amount = 0.00;

    /**
     * Constructor
     * @param int|float $amount Dollar Amount
     */
    public function __construct($amount)
    {
        $value = floatval($amount);

        if ($value < 0) {
            throw new NonAbsoluteDollarAmount;
        }

        $this->amount = $value;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        return $this->amount;
    }
}
