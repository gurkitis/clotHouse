<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\OrgUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SetAdmin extends Controller
{
    /**
     * org-setAdmin
     *
     * @param Request $request
     * @return Response
     */
    public function setAdmin(Request $request): Response
    {
        // Input data filed validation
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

        // Cannot promote to admin role an owner
        if ($request->input('user_id') == $request->attributes->get('user_id')) {
            return Response('invalid input data', 422);
        }

        // Update user role
        $orgUser = OrgUser::where('user', $request->input('user_id'))
            ->where('organization', $request->attributes->get('org_id'))
            ->first();
        $orgUser->update([
            'is_admin' => TRUE
        ]);

        return Response('user has been promoted', 204);
    }
}
