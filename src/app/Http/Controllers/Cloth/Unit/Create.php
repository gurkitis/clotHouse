<?php

namespace App\Http\Controllers\Cloth\Unit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\ClothingUnit;
use App\Models\Trans\Exchange;
use App\Http\Controllers\Trans\Create as CreateTrans;

class Create extends Controller
{
    /**
     * unit-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate warehouse type
        if ($request->attributes->get('house_type') != 'organization') {
            return Response('cannot initialize in user warehouse', 400);
        }

        // Validate input data
        try {
            $this->validate($request, [
                'identificator' => 'required|string|min:1|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        if (empty(ClothingUnit::where('clothing', $request->query('cloth_id'))
            ->where('identificator', $request->input('identificator'))
            ->get()->toArray()) === FALSE
        ) {
            return Response('invalid input data', 422);
        }

        // Create new clothing unit
        $unit = new ClothingUnit();
        $unit->fill([
            'identificator' => $request->input('identificator'),
            'status' => $request->query('stat_id'),
            'clothing' => $request->query('cloth_id'),
            'warehouse' => $request->query('house_id'),
            'organization' => $request->attributes->get('org_id')
        ]);
        $unit->save();

        // Create init transaction
        $transRequest = Request::create(
            '?house_id=' . $request->query('house_id')
            . '&user_id=' . $request->query('user_id')
            . '&unit_id=' . $unit['id'],
            'POST', ['information' => "clothing unit's initialization"]
        );
        $transRequest->attributes = $request->attributes;
        $transResponse = (new CreateTrans())->create($transRequest);
        if ($transResponse->status() != 201) {
            return Response($transResponse->content(), 500);
        }

        return Response('', 201);
    }
}
