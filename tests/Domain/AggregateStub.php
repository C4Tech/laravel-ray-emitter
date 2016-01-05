<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Domain\Aggregate;
use C4tech\RayEmitter\Contracts\Domain\Command;
use C4tech\RayEmitter\Contracts\Domain\Event;
use Codeception\Verify;
use Mockery;

class AggregateStub extends Aggregate
{
    protected $test_get = 'test';

    protected function applyTrue(Event $event)
    {
        expect(true);
    }

    protected function handleTrue(Command $command)
    {
        expect(true);

        return Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');
    }
}
