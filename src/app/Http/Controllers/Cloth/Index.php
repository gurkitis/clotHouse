<?php

namespace App\Http\Controllers\Cloth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cloth\Clothing;
use Illuminate\Http\Response;
use App\Http\Middleware\ClothAuth;

class Index extends Controller
{
    /**
     * cloth-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Check if clothing id is specified
        if (empty($request->get('cloth_id')) === FALSE) {
            // Authorize specified clothing
            $authResponse = (new ClothAuth())->handle($request);
            if ($authResponse->status() != 204) {
                return Response($authResponse->content(), 500);
            }
            // Retrieve specified clothing
            $cloth = Clothing::find($request->get('cloth_id'));
        } else {
            // Retrieve all organization's clothes
            $cloth = Clothing::where('organization', $request->attributes->get('org_id'))->get();
        }

        return Response($cloth->toJson(), 200);
    }
}
