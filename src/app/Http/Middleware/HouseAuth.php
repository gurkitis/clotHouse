<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\House\OrgHouse;
use App\Models\User\User;
use App\Http\Middleware\OrgUserAuth;

class HouseAuth
{
    public const house_type = [
        'user' => 'user',
        'org' => 'organization'
    ];

    /**
     * house-midl-auth
     *
     * @param Request $request
     * @param Closure|null $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next = NULL)
    {
        // Validate required input data fields
        if (empty($request->input('house_id'))) {
            return Response('invalid input data', 422);
        }

        // Check if warehouse is not owned by client
        if ($request->input('house_id') != $request->attributes->get('house_id')){
            // Check if warehouse is owned by organization
            $house = OrgHouse::firstWhere('warehouse', $request->input('house_id'));
            if (empty($house) === FALSE) {
                // Check if warehouse is owned by client's organization
                if ($house['organization'] == $request->attributes->get('org_id')) {
                    $request->attributes->set('house_type', self::house_type['org']);
                    // For system's internal use
                    if (empty($next) === TRUE) {
                        return Response('', 204);
                    }
                    return $next($request);
                }
                // Warehouse is owned in different organization
                return Response('access denied', 403);
            }

            $user = User::firstWhere('warehouse', $request->input('house_id'));
            // Check if warehouse exists
            if (empty($user) === TRUE) {
                return Response('warehouse not found', 404);
            }

            if (in_array($request->attributes->get('role'), ['admin', 'owner']) === TRUE) {
                // Authorize user against client
                $userAuthRequest = Request::create('', '', [
                   'user_id' => $user['id']
                ]);
                $userAuthRequest->attributes = $request->attributes;
                $userAuthResponse = (new OrgUserAuth)->handle($userAuthRequest, NULL);
                if ($userAuthResponse->status() !== 204) {
                    return Response('house-midl-auth', 500);
                }
            } else {
                // Client cannot access warehouse due to their role (user)
                return Response('access denied', 403);
            }
        }

        $request->attributes->set('house_type', self::house_type['user']);

        // For system's internal use
        if (empty($next) === TRUE) {
            return Response('', 204);
        }

        return $next($request);
    }
}
