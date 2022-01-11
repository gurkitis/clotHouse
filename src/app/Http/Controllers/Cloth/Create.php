<?php

namespace App\Http\Controllers\Cloth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\Clothing;
use App\Http\Middleware\CatAuth;
use Faker\Generator;

class Create extends Controller
{
    /**
     * cloth-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate input data
        $rules = [
            'image' => 'filled|string',
            'name' => 'required|string|min:1|max:255'
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

        // Create clothing
        $cloth = new Clothing();
        $cloth->fill(array_intersect_key($request->input(), $rules));
        $cloth->fill([
            'category' => $request->get('cat_id'),
            'organization' => $request->attributes->get('org_id')
        ]);
        $cloth->save();

        return Response('', 201);
    }
}
