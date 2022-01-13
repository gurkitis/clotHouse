<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Trans\Exchange;

class Edit extends Controller
{
    /**
     * trans-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data fields
        $rules = [
            'trans_id' => 'required|integer|exists:exchange,id',
            'information' => 'required_without:issuer_house_id,receiver_house_id|string',
            'issuer_house_id' => 'required_without:information,receiver_house_id|exists:warehouse,id',
            'receiver_house_id' => 'required_without:information,issuer_house_id|exists:warehouse,id'
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

        // Find transaction
        $trans = Exchange::find($request->input('trans_id'));

        // Edit transaction's data
        foreach ($rules as $key => $rule) {
            if (empty($request->input($key)) === TRUE) continue;
            $field = NULL;
            switch ($key) {
                case 'information':
                    $field = $key;
                    break;
                case 'issuer_house_id':
                    $field = 'issuer_warehouse';
                    break;
                case 'receiver_warehouse_id':
                    $field = 'receiver_warehouse';
                    break;
            }
            $trans->update([$field => $request->input($key)]);
        }

        return Response($trans->toJson(), 200);
    }
}
