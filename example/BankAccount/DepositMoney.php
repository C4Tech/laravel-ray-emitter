<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\Command;

final class DepositMoney extends Command
{
    /**
     * Constructor
     *
     * @param string $account_id Account identifier.
     * @param array  $data       Payload data.
     * @param int    $sequence   Expected version sequence.
     */
    public function __construct($account_id, array $data, $sequence = 0)
    {
        $this->setupPayload();
        $this->target_id = $account_id;
        $this->sequence = $sequence;
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
