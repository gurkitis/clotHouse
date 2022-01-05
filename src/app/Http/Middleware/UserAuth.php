<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User\Session;
use App\Models\Org\OrgUser;
use App\Models\User\User;
use Illuminate\Http\Response;

class UserAuth
{
    /**
     * user-auth
     *
     * @param Request $request
     * @param Closure $next
     * @param $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // Validate input data
        if (empty($request->get('org_id')) || empty($request->bearerToken())) {
            return Response('invalid input data', 422);
        }

        // Validate barer token
        $session = Session::firstWhere('session_id', $request->bearerToken());
        if (empty($session)) {
            return Response('unsuccessful authentication', 401);
        }

        // Validate barer token age
        if (date('U') - strtotime($session['last_request_at']) > strtotime('10 min', 0)) {
            return Response('please authenticate', 401);
        }

        // User exist in given organization
        $orgUser = OrgUser::where('organization', $request->get('org_id'))->where('user', $session['user'])->first();
        if (empty($orgUser)) {
            return Response("user doesn't exist in organization");
        }

        // User has access to the resource
        if (OrgUser::validateRole($orgUser->getRole(), $role) === FALSE) {
            return Response('unauthorized access', 401);
        }

        // Set 'session' variables
        $user = User::firstWhere('id', $session['user']);
        $request->attributes->add([
            'org_id' => $request->get('org_id'),
            'user_id' => $user['id'],
            'role' => $orgUser->getRole(),
            'warehouse' => $user['warehouse']
        ]);

        // Update session's last request
        $session->update([
            'last_request_at' => date(DATE_ATOM)
        ]);

        return $next($request);
    }
}
