<?php

namespace App\Http\Controllers\Cloth\Cat;

use App\Http\Controllers\Controller;
use App\Models\Cloth\Category;
use Illuminate\Http\Request;
use App\Http\Middleware\CatAuth;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Router;

class Index extends Controller
{
    /**
     * cat-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Check of category id is set
        if (empty($request->get('cat_id')) === FALSE) {
            // Authorize requested category
            $authResponse = (new CatAuth())->handle($request);
            if ($authResponse->status() !== 204) {
                return Response($authResponse->content(), 500);
            }
            // Find requested category
            $cat = Category::find($request->get('cat_id'))->toJson();
        } else {
            // Find all organization's categories
            $cat = Category::where('organization', $request->attributes->get('org_id'))->get()->toJson();
        }

        return Response($cat, 200);
    }
}
