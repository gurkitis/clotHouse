<?php

namespace App\Http\Controllers\Cloth\Stat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\Status;

class Edit extends Controller
{
    /**
     * stat-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data
        $rules = [
            'status' => 'filled|string|min:1|max:255'
        ];
        try {
            $this->validate($request, $rules);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        $stat = Status::find($request->get('stat_id'));

        // Edit status data
        $stat->update(array_intersect_key($request->input(), $rules));

        return  Response($stat->toJson(), 200);
    }
}
