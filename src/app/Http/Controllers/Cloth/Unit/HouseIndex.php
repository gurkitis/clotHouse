<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\ClothingUnit;

class HouseIndex extends Controller
{
    /**
     * unit-house-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Get warehouse's clothing units
        $units = ClothingUnit::where('warehouse', $request->input('house_id'))->get();

        return Response($units->toJson(), 200);
    }
}
