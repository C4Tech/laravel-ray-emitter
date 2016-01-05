<?php namespace C4tech\Test\RayEmitter\Domain;

use stdClass;
use C4tech\RayEmitter\Domain\Command;

class CommandStub extends Command
{
    protected $target_id = 13;

    protected $expected_sequence = 3;

    public function __construct()
    {
        $this->setupPayload();
    }

    public function run()
    {
        return true;
    }
}
