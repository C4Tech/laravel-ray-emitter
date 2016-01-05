<?php namespace C4tech\Test\RayEmitter\Domain;

use C4tech\RayEmitter\Contracts\Domain\Command as CommandInterface;
use C4tech\Support\Test\Base;

class CommandTest extends Base
{
    public function testGetAggregateId()
    {
        $subject = new CommandStub;
        expect($subject->getAggregateId())->equals(13);
    }

    public function testGetExpectedSequence()
    {
        $subject = new CommandStub;
        expect($subject->getExpectedSequence())->equals(3);
    }

    /**
     * @inheritDoc
     * @return stdClass
     */
    public function testGetPayload()
    {
        $subject = new CommandStub;
        $payload = $this->getPropertyValue($subject, 'payload');
        expect($subject->getPayload())->equals($payload);
        expect(get_class($payload))->equals('stdClass');
    }
}
