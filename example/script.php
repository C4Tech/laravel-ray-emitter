<?php

use RayEmitter\Example\BankAccount\CreateAccount;
use RayEmitter\Example\BankAccount\DepositMoney;
use RayEmitter\Example\BankAccount\GetAccountBalance;
use RayEmitter\Example\BankAccount\WithdrawMoney;

$data = [
    'owner' => 'Owner Name',
    'deposit' => 64
];
$command = new CreateAccount($data);
$command->run();

$account = '12345-45771...';
$data = ['deposit' => 10];
$command = new DepositMoney($account, $data, 0);
$command->run();

$data = ['withdrawl' => 306];
$command = new WithdrawMoney($account, $data, 1);
$command->setExpectedSequence(1)->run();

$query = new GetAccountBalance($account);
echo $query->run(); // As of sequence 1, return value should be 360.
