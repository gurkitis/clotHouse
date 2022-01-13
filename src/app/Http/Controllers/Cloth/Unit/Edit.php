<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\ClothingUnit;
use App\Http\Middleware\StatAuth;
use App\Http\Middleware\HouseAuth;

class Edit extends Controller
{
    /**
     * unit-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data
        try {
            $this->validate($request, [
                'identificator' => 'filled|string|min:1|max:255',
                'status' => 'filled|integer',
                'warehouse' => 'filled|integer'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        $unit = ClothingUnit::find($request->get('unit_id'));
        $fieldsToUpdate = [];

        // Validate identificator field
        if (empty($request->input('identificator')) === FALSE) {
            if (empty(ClothingUnit::where('clothing', $unit['clothing'])
                    ->where('identificator', $request->input('identificator'))
                    ->get()->toArray()) === FALSE) {
                return Response('invalid input data', 422);
            }
            $fieldsToUpdate['identificator'] = $request->input('identificator');
        }

        // Validate status
        if (empty($request->input('status')) === FALSE) {
            $authRequest = Request::create('', 'GET', ['stat_id' => $request->input('status')]);
            $authRequest->attributes = $request->attributes;
            $authResponse = (new StatAuth())->handle($authRequest);
            if ($authResponse->status() != 204) {
                return Response($authResponse->contnet(), 500);
            }
            $fieldsToUpdate['status'] = $request->input('status');
        }

        // Validate warehouse (only for internal use)
        if (empty($request->input('warehouse')) === FALSE && empty($request->query('org_id')) === TRUE) {
            $authRequest = Request::create('', 'GET', ['house_id' => $request->input('warehouse')]);
            $authRequest->attributes = $request->attributes;
            $authResponse = (new HouseAuth())->handle($authRequest);
            if ($authResponse->status() != 204) {
                return Response($authResponse->contnet(), 500);
            }
            $fieldsToUpdate['warehouse'] = $request->input('warehouse');
        }

        // Update clothing unit
        $unit->update($fieldsToUpdate);

        return Response($unit->toJson(), 200);
    }
}
