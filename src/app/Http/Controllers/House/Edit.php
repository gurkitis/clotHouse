<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\House\Warehouse;

class Edit extends Controller
{
    /**
     * house-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data
        $rules = [
            'address' => 'filled|string|min:1|max:255'
        ];
        try {
            $this->validate($request, $rules);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Check warehouse type org/user
        if ($request->attributes->get('house_type') === 'organization') {
            // Check if client's role
            if (in_array($request->attributes->get('role'), ['admin', 'owner']) === FALSE) {
                return Response('user access denied', 403);
            }
        } else {
            // Check if warehouse is client's warehouse
            if ($request->attributes->get('house_id') != $request->input('house_id')) {
                return Response('user access denied', 403);
            }
        }

        // Update warehouse data
        $house = Warehouse::find($request->input('house_id'));
        $house->update(array_intersect_key($request->input(), $rules));

        return Response($house->toJson(), 200);
    }
}
