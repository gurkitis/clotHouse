<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\Category;

class CatAuth
{
    /**
     * cat-midl-auth
     *
     * @param Request $request
     * @param Closure|null $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next = NULL)
    {
        // Validate input data
        if (empty($request->get('cat_id')) === TRUE) {
            return Response('invalid input data', 422);
        }

        $cat = Category::find($request->get('cat_id'));

        // Validate category's existence
        if (empty($cat) === TRUE) {
            return Response('category not found', 404);
        }

        // Validate category's organization
        if ($cat['organization'] != $request->attributes->get('org_id')) {
            return Response('access denied', 403);
        }

        // For system's internal use
        if (empty($next) === TRUE) {
            return Response('', 204);
        }

        return $next($request);
    }
}
