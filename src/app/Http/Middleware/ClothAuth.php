<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\Clothing;

class ClothAuth
{
    /**
     * cloth-midl-auth
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Validate required input data
        if (empty($request->get('cloth_id')) === TRUE) {
            return Response('invalid input data', 422);
        }

        $cloth = Clothing::find($request->get('cloth_id'));

        // Validate clothing existence
        if (empty($cloth) === TRUE) {
            return Response('clothing not found', 404);
        }

        // Validate client access
        if ($cloth['organization'] != $request->attributes->get('org_id')) {
            return Response('access denied', 403);
        }

        return $next($request);
    }
}
