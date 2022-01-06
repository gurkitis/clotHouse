<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use App\Models\House\OrgHouse;
use App\Models\House\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class OrgCreate extends Controller
{
    public function create(Request $request)
    {
        // Validate input data
        try {
            $this->validate($request, [
                'address' => 'required|string|min:1|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Create warehouse
        $warehouse = new Warehouse();
        $warehouse->fill([
            'address' => $request->post('address')
        ]);
        $warehouse->save();

        // Create organization_warehouse
        $orgHouse = new OrgHouse();
        $orgHouse->fill([
            'organization' => $request->get('org_id'),
            'warehouse' => $warehouse['id']
        ]);
        $orgHouse->save();

        return Response('', 201);
    }
}
