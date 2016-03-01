<?php namespace C4tech\RayEmitter\Event;

use Closure;
use Exception;
use C4tech\RayEmitter\Facades\EventStore;
use Illuminate\Support\Facades\DB;

class Middleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();

        try {
            $response = $next($request);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        EventStore::publishQueue();

        return $response;
    }
}
