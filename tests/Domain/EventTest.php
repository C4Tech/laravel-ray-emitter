<?php namespace C4tech\Test\RayEmitter\Domain;

use stdClass;
use DateTime;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class EventTest extends Base
{
    protected $subject;
    protected $identifier = 'abc-123';
    protected $payload = ['test' => true, 'count' => 4];

    public function setUp()
    {
        $this->subject = new EventStub($this->identifier, $this->payload);
    }

    public function testConstructor()
    {
        $identifier = $this->getPropertyValue($this->subject, 'identifier');
        expect($identifier)->equals($this->identifier);
        $payload = $this->getPropertyValue($this->subject, 'payload');
        expect($payload->test)->equals($this->payload['test']);
        expect($payload->count)->equals($this->payload['count']);
    }

    public function testGetId()
    {
        expect($this->subject->getId())->equals($this->identifier);
    }

    public function testGetPayload()
    {
        $payload = $this->subject->getPayload();
        expect($payload->test)->equals($this->payload['test']);
        expect($payload->count)->equals($this->payload['count']);
    }

    public function testGetSequence()
    {
        $value = 13;

        $this->setPropertyValue($this->subject, 'sequence', $value);
        expect($this->subject->getSequence())->equals($value);
    }

    public function testGetTimestamp()
    {
        $value = 8675309;

        $this->setPropertyValue($this->subject, 'timestamp', $value);
        expect($this->subject->getTimestamp())->equals($value);
    }

    public function testSerialize()
    {
        $payload = new stdClass;
        $payload->date = new DateTime;

        $value = new stdClass;
        $value->class = DateTime::class;
        $value->value = json_encode($payload->date);

        $this->setPropertyValue($this->subject, 'payload', $payload);

        $json = $this->subject->serialize();
        $data = json_decode($json);

        expect($data->date)->equals($value);
    }

    public function testUnserialize()
    {
        $value = 'a piece of datum';
        $sequence = 23;

        $payload = new stdClass;
        $payload->settings = new ValueObjectStub($value);

        $value = new stdClass;
        $value->class = ValueObjectStub::class;
        $value->value = json_encode($payload->settings);

        $record = new stdClass;
        $record->identifier = $this->identifier;
        $record->sequence = $sequence;
        $record->payload = json_encode(['settings' => $value]);
        $record->created_at = new DateTime;

        $event = EventStub::unserialize($record);
        expect($event->getSequence())->equals($sequence);
        expect($event->getPayload())->equals($payload);
        expect($event->getTimestamp())->equals($record->created_at);
    }
}
