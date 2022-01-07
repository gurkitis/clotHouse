<?php

namespace App\Http\Controllers\Org;

use App\Http\Controllers\Controller;
use App\Models\Org\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;

class Edit extends Controller
{
    /**
     * org-edit
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function edit(Request $request)
    {
        // Find requested organization
        $org = Organization::find($request->attributes->get('org_id'));

        // Validate input data
        if (empty($org) === TRUE) {
            return Response('invalid input data', 422);
        }

        try {
            $this->validate($request, [
                'name' => 'required|string|min:1|max:255|unique:organization,name'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Edit organization data
        $org->update([
            'name' => $request->post('name')
        ]);

        return Response(json_encode([
            'name' => $org['name']
        ]), 200);
    }
}
