<?php

namespace App\Http\Controllers\Cloth;

use App\Http\Controllers\Controller;
use App\Models\Cloth\Clothing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Http\Middleware\CatAuth;

class Edit extends Controller
{
    /**
     * cloth-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data
        $rules = [
            'name' => 'filled|string|min:1|max:255',
            'category' => 'filled|integer'
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

        // Validate category if it exists
        if (empty($request->post('category')) === FALSE) {
            $authRequest = Request::create('', 'GET', [
                'cat_id' => $request->post('category'),
            ]);
            $authRequest->attributes = $request->attributes;
            $authResponse = (new CatAuth())->handle($authRequest);
            if ($authResponse->status() != 204) {
                return Response($authResponse->content(), 500);
            }
        }

        // Edit clothing data
        $cloth = Clothing::find($request->get('cloth_id'));
        $cloth->update(array_intersect_key($request->input(), $rules));

        return Response($cloth->toJson(), 200);
    }
}
