<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User\User as UserModel;
use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\ResponseFactory;
use App\Http\Controllers\House\Warehouse;
use App\Models\House\Warehouse as WarehouseModel;

class Show extends Controller
{
    /**
     * user-show
     *
     * @param Request $request
     * @param $id
     * @return Response|ResponseFactory
     */
    public function show(Request $request, $id = NULL) {
        // Validate GET params
        try {
            $this->validate($request, [
                'email' => 'nullable|email|min:5|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Validate URI params
        if ($id === NULL && empty($request->get('email')) === TRUE) {
            return Response('invalid input data', 422);
        }

        // Search user
        $user = collect(UserModel::all());
        if ($id !== NULL) {
            $user = $user->where('id', $id);
        }
        if (empty($request->get('email')) === FALSE) {
            $user = $user->where('email', $request->get('email'));
        }

        // Process Response
        $user = $user->first();
        if (empty($user) === FALSE) {
            $user->makeHidden('warehouse');
            $user = $user->toJson();
            return Response($user, 200);
        } else {
            return Response(json_encode (json_decode ("{}")), 200);
        }
    }
}
