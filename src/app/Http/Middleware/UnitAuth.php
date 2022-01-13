<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\ClothingUnit;

class UnitAuth
{
    /**
     * unit-midl-auth
     *
     * @param Request $request
     * @param Closure|null $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next = NULL)
    {
        // Validates required input data
        if (empty($request->get('unit_id')) === TRUE) {
            return Response('invalid input data', 422);
        }

        $unit = ClothingUnit::find($request->get('unit_id'));

        // Validate clothing unit's existence
        if (empty($unit) === TRUE) {
            return Response('clothing unit not found', 404);
        }

        // Validate clothing unit's organization
        if ($unit['organization'] != $request->attributes->get('org_id')) {
            return Response('access denied', 403);
        }

        // For system's internal use
        if (empty($next) === TRUE) {
            return Response('', 204);
        }

        return $next($request);
    }
}
