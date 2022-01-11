<?php

namespace App\Http\Controllers\Cloth;

use App\Http\Controllers\Controller;
use App\Models\Cloth\Clothing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Delete extends Controller
{
    /**
     * cloth-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Find clothing
        $cloth = Clothing::find($request->get('cloth_id'));

        // Check clothingUnits dependencies
        if (empty($cloth->clothingUnits()->get()->toArray()) === FALSE) {
            return Response('cannot delete, entity has dependency', 400);
        }

        // Delete clothing
        $cloth->delete();

        return Response('', 204);
    }
}
