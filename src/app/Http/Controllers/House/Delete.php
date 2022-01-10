<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\House\Warehouse;

class Delete extends Controller
{
    /**
     * house-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request)
    {
        // Validate input data
        try {
            $this->validate($request, [
                'house_id' => 'required|integer'
            ]);
        } catch (ValidationException $e) {
            if(env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 500);
            }
        }

        // Validate warehouse existence
        $house = Warehouse::find($request->input('house_id'));
        if (empty($house) === TRUE) {
            return Response('warehouse not found', 500);
        }

        // Delete warehouse
        $house->delete();

        return Response(204);
    }
}
