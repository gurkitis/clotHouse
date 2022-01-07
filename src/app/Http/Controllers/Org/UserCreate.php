<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Org\OrgUser;

class UserCreate extends Controller
{
    /**
     * org-userCreate
     *
     * @param Request $request
     * @return Response
     */
    public function userCreate(Request $request): Response
    {
        // Validate required input data fields
        try {
            $this->validate($request, [
                'user_id' => 'required|integer|exists:user,id',
                'org_id' => 'required|integer|exists:organization,id'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 500);
            } else {
                return Response('invalid input data', 500);
            }
        }

        // Add user to organization
        $orgUser = new OrgUser();
        $orgUser->fill([
            'is_admin' => FALSE,
            'is_owner' => FALSE,
            'user' => $request->input('user_id'),
            'organization' => $request->input('org_id')
        ]);
        $orgUser->save();

        return Response('user has been added to organization', 201);
    }
}
