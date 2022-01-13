<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use App\Models\Cloth\ClothingUnit;
use App\Models\Trans\Exchange;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Cloth\Unit\Edit;

class Create extends Controller
{
    /**
     * trans-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate input data
        try {
            $this->validate($request, [
                'inforamtion' => 'filled|string'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Get clothing unit
        $unit = ClothingUnit::find($request->query('unit_id'));

        // Edit clothing unit warehouse
        $editRequest = Request::create('?unit_id=' . $unit['id'], 'PUT', [
            'warehouse' => $request->query('house_id')
        ]);
        $editRequest->attributes = $request->attributes;
        $editResponse = (new Edit())->edit($request);
        if ($editResponse->status() != 200) {
            return Response($editResponse->content(), 500);
        }

        // Find previous transaction
        $prevTrans = Exchange::orderByDesc('date')->firstWhere('clothing_unit', $unit['id']);

        // Create new transaction
        $trans = new Exchange();
        $trans->fill([
            'date' => date(DATE_ATOM),
            'information' => (empty($request->input('information'))) ? NULL : $request->input('information'),
            'clothing_unit' => $unit['id'],
            'issuer_warehouse' => (empty($prevTrans)) ? NULL : $prevTrans['receiver_warehouse'],
            'receiver_warehouse' => $request->query('house_id'),
            'facilitator' => $request->attributes->get('user_id')
        ]);
        $trans->save();

        return Response('', 201);
    }
}
