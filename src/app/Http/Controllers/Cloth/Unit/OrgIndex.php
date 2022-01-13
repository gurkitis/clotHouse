<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\ClothingUnit;

class OrgIndex extends Controller
{
    /**
     * unit-org-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Find clothing unit's associated with organization
        $units = ClothingUnit::where('organization', $request->attributes->get('org_id'))->get();

        return Response($units->toJson(), 200);
    }
}
