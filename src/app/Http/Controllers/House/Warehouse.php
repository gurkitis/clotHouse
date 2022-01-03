<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use App\Models\House\Warehouse as WarehouseModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class Warehouse extends Controller
{
    /**
     * house-create
     *
     * @param string|NULL $address
     * @return Response|ResponseFactory
     */
    public function create(string $address = NULL) {
        $warehouse = new WarehouseModel();
        if (empty($address) === FALSE) {
            $warehouse->fill([
                'address' => $address
            ]);
        }
        if ($warehouse->save() === TRUE) {
            return Response('', 201);
        } else {
            return Response('', 500);
        }
    }
}
