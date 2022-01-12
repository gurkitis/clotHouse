<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\Status;

class StatAuth
{
    /**
     * stat-midl-auth
     *
     * @param Request $request
     * @param Closure|null $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next = NULL)
    {
        // Validate required input data
        if (empty($request->get('stat_id')) === TRUE) {
            return Response('invalid input data', 422);
        }

        $stat = Status::find($request->get('stat_id'));

        // Validate status existence
        if (empty($stat) === TRUE) {
            return Response('status not found', 404);
        }

        // Validate status organization
        if ($stat['organization'] != $request->attributes->get('org_id')) {
            return Response('access denied', 403);
        }

        // For system's internal use
        if (empty($next) === TRUE) {
            return Response('', 204);
        }

        return $next($request);
    }
}
