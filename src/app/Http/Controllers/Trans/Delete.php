<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Trans\Exchange;

class Delete extends Controller
{
    /**
     * trans-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Validate required input data fields
        try {
            $this->validate($request, [
                'unit_id' => 'required|integer|exists:clothing_unit,id'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 500);
            } else {
                return Response('invalid input data', 500);
            }
        }

        // Delete transactions connected to clothing unit
        $trans = Exchange::where('clothing_unit', $request->input('unit_id'))->get();
        foreach ($trans as $tran) {
            $tran->delete();
        }

        return Response('', 204);
    }
}
