<?php namespace C4tech\RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\Command;

final class CreateAccount extends Command
{
    /**
     * Constructor
     *
     * @param array $data Payload data.
     */
    public function __construct(array $data)
    {
        $this->setupPayload();
        $this->payload->owner = $data['owner'];
        $this->payload->deposit = $data['deposit'];
    }

    /**
     * Run
     *
     * Push the command into the Aggregate.
     * @return void
     */
    public function run()
    {
        Aggregate::handle($this);
    }
}
