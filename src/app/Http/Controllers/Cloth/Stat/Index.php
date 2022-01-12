<?php

namespace App\Http\Controllers\Cloth\Stat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Middleware\StatAuth;
use App\Models\Cloth\Status;

class Index extends Controller
{
    /**
     * stat-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Check if status is specified
        if (empty($request->get('stat_id')) === FALSE) {
             // Authorize requested status
            $authResponse = (new StatAuth())->handle($request);
            if ($authResponse->status() != 204) {
                return Response($authResponse->content(), 500);
            }
            // Find requested status
            $stat = Status::find($request->get('stat_id'));
        } else {
            // Find all organization's statuses
            $stat = Status::where('organization', $request->attributes->get('org_id'))->get();
        }

        return Response($stat->toJson(), 200);
    }
}
