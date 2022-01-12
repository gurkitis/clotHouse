<?php

namespace App\Http\Controllers\Cloth\Stat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\Status;

class Create extends Controller
{
    /**
     * stat-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate input data
        $rules = [
            'status' => 'required|string|min:1|max:255'
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

        // Create new status
        $stat = new Status();
        $stat->fill(array_intersect_key($request->input(), $rules));
        $stat->fill(['organization' => $request->attributes->get('org_id')]);
        $stat->save();

        return Response('', 201);
    }
}
