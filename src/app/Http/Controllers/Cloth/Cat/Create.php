<?php

namespace App\Http\Controllers\Cloth\Cat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\Category;

class Create extends Controller
{
    /**
     * cat-create
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        // Validate input data
        try {
            $this->validate($request, [
                'name' => 'required|string|min:1|max:255'
            ]);
        } catch (ValidationException $e) {
            if (env('APP_ENV') === 'local') {
                return Response($e, 422);
            } else {
                return Response('invalid input data', 422);
            }
        }

        // Create category
        $cat = new Category();
        $cat->fill([
            'name' => $request->post('name'),
            'organization' => $request->attributes->get('org_id')
        ]);
        $cat->save();

        return Response('', 201);
    }
}
