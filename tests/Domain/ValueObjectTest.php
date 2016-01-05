<?php namespace C4tech\Test\RayEmitter\Domain;

use DateTime;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Mockery;

class ValueObjectTest extends Base
{
    protected $data = ['test' => true, 'count' => 4];

    public function setUp()
    {
        $this->subject = new ValueObjectStub($this->data);
    }

    public function testJsonSerialize()
    {
        expect($this->subject->jsonSerialize())->equals($this->data);
        expect(json_encode($this->subject))->equals(json_encode($this->data));
    }

    public function testJsonUnserialize()
    {
        $json = json_encode($this->data);
        $subject = ValueObjectStub::jsonUnserialize($json);

        expect($subject->data)->equals($this->data);
    }

    public function testEqualsIsFalseWhenDifferentClass()
    {
        $other = new ValueObjectStubOther($this->data);
        expect($this->subject->equals($other))->equals(false);
    }

    public function testEqualsIsFalseWhenDifferentValue()
    {
        $other = new ValueObjectStub(['magic']);
        expect($this->subject->equals($other))->equals(false);
    }

    public function testEqualsIsTrueWhenSame()
    {
        $other = new ValueObjectStub($this->data);
        expect($this->subject->equals($other))->equals(true);
    }
}
