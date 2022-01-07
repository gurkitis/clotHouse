<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserIndex extends Controller
{
    public function userIndex(Request $request): Response
    {
        $orgUsers = Organization::find($request->attributes->get('org_id'))->orgUsers()->get();
        $result = [];
        foreach ($orgUsers as $orgUser) {
            $result[] = [
                'id' => $orgUser->user()->first()['id'],
                'role' => $orgUser->getRole()
            ];
        }

        return Response(json_encode($result), 200);
    }
}
