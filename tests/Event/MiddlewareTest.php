<?php namespace C4tech\Test\RayEmitter\Event;

use C4tech\RayEmitter\Event\Middleware;
use C4tech\RayEmitter\Facades\EventStore;
use C4tech\Support\Test\Base;
use Codeception\Verify;
use Illuminate\Support\Facades\DB;
use Mockery;

class MiddlewareTest extends Base
{
    public function tearDown()
    {
        parent::tearDown();
        DB::clearResolvedInstances();
        EventStore::clearResolvedInstances();
    }

    /**
     * @expectedException Exception
     */
    public function testHandleRollsBack()
    {
        $subject = new Middleware;
        $parameter = 'request';
        $callback = function ($request) use ($parameter) {
            expect($request)->equals($parameter);
            throw new \Exception;
        };

        DB::shouldReceive('beginTransaction')
            ->withNoArgs()
            ->once();

        EventStore::shouldReceive('saveQueue')
            ->withNoArgs()
            ->never();

        DB::shouldReceive('rollBack')
            ->withNoArgs()
            ->once();

        DB::shouldReceive('commit')
            ->withNoArgs()
            ->never();

        expect_not($subject->handle($parameter, $callback));
    }

    public function testHandleCommits()
    {
        $subject = new Middleware;
        $parameter = 'request';
        $response = 'magic beans';
        $callback = function ($request) use ($parameter, $response) {
            expect($request)->equals($parameter);
            return $response;
        };

        DB::shouldReceive('beginTransaction')
            ->withNoArgs()
            ->once();

        EventStore::shouldReceive('publishQueue')
            ->withNoArgs()
            ->once();

        DB::shouldReceive('rollBack')
            ->withNoArgs()
            ->never();

        DB::shouldReceive('commit')
            ->withNoArgs()
            ->once();

        expect($subject->handle('request', $callback))->equals($response);
    }
}
