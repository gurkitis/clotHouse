<?php

namespace App\Http\Controllers\Cloth\Cat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cloth\Clothing;
use Illuminate\Http\Response;
use App\Models\Cloth\Category;

class Delete extends Controller
{
    /**
     * cat-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Check if category has clothing dependency
        if (empty(Clothing::where('category', $request->get('cat_id'))->get()->toArray()) === FALSE) {
            return Response('cannot delete, entity has dependency', 400);
        }

        // Delete category
        Category::find($request->get('cat_id'))->delete();

        return Response('', 204);
    }
}
