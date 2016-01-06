<?php namespace C4tech\Test\RayEmitter\Event;

use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class CollectionTest extends Base
{
    public function testAppend()
    {
        $value = Mockery::mock('C4tech\RayEmitter\Contracts\Domain\Event');
        $subject = Mockery::mock('C4tech\RayEmitter\Event\Collection[push]');

        $subject->shouldReceive('push')
            ->with($value)
            ->once();

        $subject->append($value);
    }
}
