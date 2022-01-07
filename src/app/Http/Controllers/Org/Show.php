<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\Organization;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class Show extends Controller
{
    /**
     * org-show
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function show(Request $request)
    {
        if (empty($request->attributes->get('org_id')) === FALSE) {
            // Organization id is set -> find the organization
            $org = Organization::find($request->attributes->get('org_id'));
            return Response($org->toJson(), 200);
        } else {
            // Organization id isn't set -> find all organizations connected to user
            $user = User::find($request->attributes->get('user_id'));
            $userOrgs = $user->organizations();
            $result = [];
            foreach ($userOrgs as $userOrg) {
                $result[] = Organization::find($userOrg['organization'])->toArray();
            }
            return Response(json_encode($result), 200);
        }
    }
}
