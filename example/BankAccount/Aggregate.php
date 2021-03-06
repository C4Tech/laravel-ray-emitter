<?php namespace RayEmitter\Example\BankAccount;

use C4tech\RayEmitter\Domain\Aggregate as AbstractAggregate;
use Ramsey\Uuid\Uuid;

final class Aggregate extends AbstractAggregate
{
    /**
     * Minimum needed to open an account.
     * @var int|float
     */
    const MINIMUM_TO_OPEN = 50;

    /**
     * Account Was Created Event Handle
     *
     * @param  AccountWasCreated $event Event Object
     * @return void
     */
    protected function applyAccountWasCreated(AccountWasCreated $event)
    {
        $data = $event->getPayload();
        $identifier = new AccountId($event->getId());
        $this->root = new AggregateRoot($identifier);
        $this->root->owner = $data->owner;
        $this->root->balance = $data->deposit;
    }

    /**
     * Money Deposited Event Handle
     *
     * @param  MoneyDeposited $event Event Object
     * @return void
     */
    protected function applyMoneyDeposited(MoneyDeposited $event)
    {
        $data = $event->getPayload();
        $balance = $this->root->balance->getValue();
        $balance += $data->deposit->getValue();
        $this->root->balance = new UsDollar($balance);
    }

    /**
     * Money Withdrawn Event Handle
     *
     * @param  MoneyWithdrawn $event Event Object
     * @return void
     */
    protected function applyMoneyWithdrawn(MoneyWithdrawn $event)
    {
        $data = $event->getPayload();
        $balance = $this->root->balance->getValue();
        $balance -= $data->withdrawal->getValue();
        $this->root->balance = new UsDollar($balance);
    }

    /**
     * Create Account Command Handle
     *
     * Processes business logic for the account creation command.
     * @param  CreateAccount $command Command object.
     * @return AccountWasCreated      Event object.
     */
    protected function handleCreateAccount(CreateAccount $command)
    {
        $data = $command->getPayload();

        $account_id = new AccountId(Uuid::uuid4()->toString());
        $owner = new OwnerName($data->owner);
        $deposit = new UsDollar($data->deposit);

        if ($deposit->getValue() < self::MINIMUM_TO_OPEN) {
            throw new MinimumDepositException(sprintf(
                'The deposit of $%s is under the required minimum of $%s',
                $deposit->getValue(),
                self::MINIMUM_TO_OPEN
            ));
        }

        return new AccountWasCreated($account_id->getValue(), compact('owner', 'deposit'));
    }

    /**
     * Deposit Money Command Handle
     *
     * Processes business logic for the deposit money command.
     * @param  DepositMoney $command Command object.
     * @return MoneyDeposited        Event object.
     */
    protected function handleDepositMoney(DepositMoney $command)
    {
        $data = $command->getPayload();
        $deposit = new UsDollar($data->deposit);

        if ($deposit->getValue() < 0) {
            throw new MinimumDepositException('You cannot deposit a negative amount.');
        }

        return new MoneyDeposited($command->getId(), compact('deposit'));
    }

    /**
     * Withdraw Money Command Handle
     *
     * Processes business logic for the withdraw money command.
     * @param  WithdrawMoney $command Command object.
     * @return MoneyWithdrawn         Event object.
     */
    protected function handleWithdrawMoney(WithdrawMoney $command)
    {
        $data = $command->getPayload();
        $withdrawal = new UsDollar($data->withdrawal);

        if ($withdrawal->getValue() < 0) {
            throw new MinimumWithdrawalException('You cannot withdraw a negative amount.');
        }

        if ($withdrawal->getValue() > $this->root->balance->getValue()) {
            throw new InsufficientFundsException(sprintf(
                'Insufficient funds. You tried to withdraw $%s but only have $%s in your account.',
                $withdrawal->getValue(),
                $this->balance->getValue()
            ));
        }

        return new MoneyWithdrawn($command->getId(), compact('withdrawal'));
    }
}
