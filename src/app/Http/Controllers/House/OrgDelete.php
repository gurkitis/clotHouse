<?php

namespace App\Http\Controllers\House;

use App\Http\Controllers\Controller;
use App\Models\Cloth\ClothingUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\House\OrgHouse;
use App\Models\House\Warehouse;
use App\Http\Controllers\Cloth\Unit\HouseIndex;
use App\Http\Controllers\Trans\Create as TransCreate;
use App\Http\Controllers\Cloth\Unit\Delete as UnitDelete;
use App\Http\Controllers\Trans\Show as TransShow;
use App\Http\Controllers\Trans\Edit as TransEdit;

class OrgDelete extends Controller
{
    /**
     * house-org-delete
     *
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request): Response
    {
        // Check if delete warehouse isn't last
        if (count(OrgHouse::where('organization', $request->attributes->get('org_id'))->get()->toArray()) < 2
            && empty($request->query('org_id')) === FALSE
        ) {
            return Response('cannot delete last warehouse, please delete organization', 400);
        }

        // Find all clothing units
        $indexResponse = (new HouseIndex())->index($request);
        if ($indexResponse->status() != 200) {
            return Response($indexResponse->content(). 'house-index', 500);
        }
        $units = json_decode($indexResponse->content(), TRUE);

        if (empty($request->input('transfer_house')) === FALSE) {
            // Validate transfer house
            $transferHouse = Warehouse::find($request->input('transfer_house'));
            if (empty($transferHouse) === TRUE) {
                return Response('warehouse not found', 404);
            }
            if (OrgHouse::firstWhere('warehouse', $transferHouse['id'])['organization'] != $request->attributes->get('org_id')) {
                return Response('access denied', 403);
            }
            // Transfer all clothing units
            foreach ($units as $unit) {
                $transRequest = Request::create(
                    '?house_id=' . $transferHouse['id']
                    . '&user_id=' . $request->attributes->get('user_id')
                    . '&unit_id=' . $unit['id'],
                    'POST', ['information' => 'Transfer due to deletion']
                );
                $transRequest->attributes = $request->attributes;
                $transResponse = (new TransCreate())->create($transRequest);
                if ($transResponse->status() != 201) {
                    return Response($transResponse->content(). 'transfer-create', 500);
                }
            }

        } else {
            // Delete all clothing units
            foreach ($units as $unit) {
                $deleteRequest = Request::create('', 'GET', ['unit_id' => $unit['id']]);
                $deleteResponse = (new UnitDelete())->delete($deleteRequest);
                if ($deleteResponse->status() != 204) {
                    return Response($deleteResponse->content(). 'unit-delete', 500);
                }
            }
        }

        // Delete organization's warehouse (orgHouse)
        OrgHouse::firstWhere('warehouse', $request->query('house_id'))->delete();

        // Find all transactions where delete warehouse is used
        $showRequest = Request::create('', 'GET', [
            'receiver_house_id' => $request->query('house_id')
        ]);
        $showResponse = (new TransShow())->show($showRequest);
        if ($showResponse->status() != 200) {
            return Response($showResponse->content(). 'trans-show-1', 500);
        }
        $trans = json_decode($showResponse->content(), TRUE);
        $showRequest = Request::create('', 'GET', [
            'issuer_house_id' => $request->query('house_id')
        ]);
        $showResponse = (new TransShow())->show($showRequest);
        if ($showResponse->status() != 200) {
            return Response($showResponse->content(), 'trans-show-2', 500);
        }
        $trans = array_merge($trans, json_decode($showResponse->content(), TRUE));

        // Edit transactions where delete warehouse is used
        foreach ($trans as $tran) {
            $editRequest = NULL;
            if ($tran['issuer_warehouse'] == $request->query('house_id')) {
                $editRequest = Request::create('?trans_id=' . $tran['id'], 'PUT', [
                    'information' => $tran['information'] . '\n' . 'Original issuer warehouse(deleted): '
                        . Warehouse::find($tran['issuer_warehouse'])->toJson(),
                    'issuer_warehouse' => NULL
                ]);
            } else if ($tran['receiver_warehouse'] == $request->query('house_id')) {
                $editRequest = Request::create('?trans_id=' . $tran['id'], 'PUT', [
                    'information' => $tran['information'] . '\n' . 'Original receiver warehouse(deleted): '
                        . Warehouse::find($tran['receiver_warehouse'])->toJson(),
                    'receiver_warehouse' => NULL
                ]);
            }
            $editResponse = (new TransEdit())->edit($editRequest);
            if ($editResponse->status() != 200) {
                return Response($editResponse->content(). 'trans-edit', 500);
            }
        }

        // Delete warehouse
        Warehouse::find($request->query('house_id'))->delete();

        return Response('', 204);
    }
}
