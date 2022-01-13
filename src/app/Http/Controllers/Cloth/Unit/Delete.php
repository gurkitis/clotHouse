<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use App\Models\Cloth\ClothingUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Trans\Delete as TransDelete;

class Delete extends Controller
{
    /**
     * unit-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Find clothing unit
        $unit = ClothingUnit::find($request->query('unit_id'));

        // Delete clothing unit's transactions
        $deleteResponse = (new TransDelete())->delete($request);
        if ($deleteResponse->status() != 204) {
            return Response($deleteResponse->content(), 500);
        }

        // Delete clothing unit
        $unit->delete();

        return Response('', 204);
    }
}
