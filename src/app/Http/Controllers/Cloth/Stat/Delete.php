<?php

namespace App\Http\Controllers\Cloth\Stat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Cloth\Status;

class Delete extends Controller
{
    /**
     * stat-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Find status
        $stat = Status::find($request->get('stat_id'));

        // Check status dependencies
        if (empty($stat->clothingUnits()->get()->toArray()) === FALSE) {
            return Response('cannot delete, entity has dependency', 400);
        }

        // Delete status
        $stat->delete();

        return Response('', 204);
    }
}
