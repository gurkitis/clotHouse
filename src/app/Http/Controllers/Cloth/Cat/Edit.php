<?php

namespace App\Http\Controllers\Cloth\Cat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use App\Models\Cloth\Category;

class Edit extends Controller
{
    /**
     * cat-edit
     *
     * @param Request $request
     * @return Response
     */
    public function edit(Request $request): Response
    {
        // Validate input data
        $rules = [
            'name' => 'filled|string|min:1|max:255'
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

        // Update category data
        $cat = Category::find($request->get('cat_id'));
        $cat->update(array_intersect_key($request->post(), $rules));

        return Response($cat->toJson(), 200);
    }
}
