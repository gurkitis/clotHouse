<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Trans\Exchange;

class Show extends Controller
{
    /**
     * trans-show
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        // Validate required input data fields
        $rules = [
            'unit_id' => 'required_without_all:user_id,issuer_house_id,receiver_house_id|integer|exists:clothing_unit,id',
            'user_id' => 'required_without_all:unit_id,issuer_house_id,receiver_house_id|integer|exists:user,id',
            'issuer_house_id' => 'required_without_all:unit_id,user_id,receiver_house_id|integer|exists:warehouse,id',
            'receiver_house_id' => 'required_without_all:unit_id,user_id,issuer_house_id|integer|exists:warehouse,id',
        ];
        try {
            $this->validate($request, $rules);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 500);
            } else {
                return Response('invalid input data', 500);
            }
        }

        // Find transactions by parameters
        $trans = Exchange::all();
        foreach ($rules as $key => $rule) {
            if (empty($request->input($key)) === TRUE) continue;
            $field = NULL;
            switch ($key) {
                case 'unit_id':
                    $field = 'clothing_unit';
                    break;
                case 'user_id':
                    $field = 'facilitator';
                    break;
                case 'issuer_house_id':
                    $field = 'issuer_warehouse';
                    break;
                case 'receiver_warehouse_id':
                    $field = 'receiver_warehouse';
                    break;
            }
            $trans = $trans->where($field, '=', $request->input($key));
        }

        return Response($trans->toJson(), 200);
    }
}
