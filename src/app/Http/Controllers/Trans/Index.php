<?php

namespace App\Http\Controllers\Trans;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Trans\Exchange;

class Index extends Controller
{
    /**
     * trans-index
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // Find all clothing unit's transactions
        $trans = Exchange::where('clothing_unit', $request->query('unit_id'))->get();

        return Response($trans->toJson(), 200);
    }
}
