<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\User\User;
use App\Models\Org\OrgUser;

class RemoveAdmin extends Controller
{
    /**
     * org-removeAdmin
     *
     * @param Request $request
     * @return Response
     */
    public function removeAdmin(Request $request): Response
    {
        // Validate required input data fields
        try {
            $this->validate($request, [
                'user_id' => 'required|integer|exists:user,id'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Validates if requested user isn't organization's owner
        if ($request->attributes->get('user_id') == $request->input('user_id')) {
            return Response('invalid input data', 422);
        }

        // Demote users role
        $orgUser = OrgUser::where('user', $request->input('user_id'))
            ->where('organization', $request->attributes->get('org_id'))
            ->first();
        $orgUser->update([
            'is_admin' => FALSE
        ]);

        return Response('user has been demoted', 204);
    }
}
