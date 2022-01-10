<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\House\OrgHouse;
use App\Models\House\Warehouse;
use Illuminate\Http\Response;

class OrgIndex extends Controller
{
    public function index(Request $request)
    {
        // Find all organization's warehouses
        $orgHouses = OrgHouse::where('organization', $request->attributes->get('org_id'))->get();

        // Extract warehouse data
        $result = [];
        foreach ($orgHouses as $orgHouse) {
            $result[] = Warehouse::find($orgHouse['warehouse'])->toArray();
        }

        return Response(json_encode($result), 200);
    }
}
