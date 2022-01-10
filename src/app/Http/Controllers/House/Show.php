<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\House\Warehouse;
use Illuminate\Http\Response;

class Show extends Controller
{
    /**
     * house-show
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        // Find and return warehouse data
        $house = Warehouse::find($request->input('house_id'));
        return Response($house->toJson(), 200);
    }
}
