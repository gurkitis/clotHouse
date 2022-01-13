<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use App\Models\Cloth\ClothingUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\UnitAuth;
use App\Http\Middleware\HouseAuth;

class Index extends Controller
{
    /**
     * unit-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Check if clothing unit id is specified
        if (empty($request->get('unit_id')) === FALSE) {
            // Authorize requested clothing unit
            $authResponse = (new UnitAuth())->handle($request);
            if ($authResponse->status() != 204) {
                return Response($authResponse->content(), 500);
            }

            // Find requested clothing unit
            $unit = ClothingUnit::find($request->get('unit_id'));

            // Authorize clothing unit's warehouse
            $authRequest = Request::create('', 'GET', ['house_id' => $unit['warehouse']]);
            $authRequest->attributes = $request->attributes;
            $authResponse = (new HouseAuth())->handle($authRequest);
            if ($authResponse->status() != 204) {
                return Response($authResponse->content(), 500);
            }
        } else {
            // Find all user's clothing units
            $unit = ClothingUnit::where('warehouse', $request->attributes->get('house_id'))->get();
        }

        return Response($unit->toJson(), 200);
    }
}
