<?php

namespace App\Http\Middleware;

use App\Models\User\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrgUserAuth
{
    /**
     * org-user-midl-auth
     *
     * @param Request $request
     * @param Closure|null $next
     * @return mixed
     */
    public function handle(Request $request, ?Closure $next)
    {
        //  Validate input
        if (empty($request->input('user_id'))) {
            return Response('invalid input data', 422);
        }

        // Check if requested user is client
        if ($request->attributes->get('user_id') !== $request->input('user_id')) {
            $user = User::firstWhere('id', $request->input('user_id'));

            // Check if requested user exist
            if (empty($user)) {
                return Response('user not found', 404);
            }

            // Check if client has access to user
            if (empty($user->organizations()->firstWhere('organization', $request->get('org_id')))){
                return Response('denied access', 403);
            }
        }

        if (empty($next) === TRUE) {
            return Response('', 204);
        }

        return $next($request);
    }
}
