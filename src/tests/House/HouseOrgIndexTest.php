<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Http\Controllers\House\OrgCreate;
use Illuminate\Http\Request;
use App\Models\House\Warehouse;

class HouseOrgIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful organization's warehouse data retrieval
     *
     * @return void
     */
    public function test_house_org_index_200()
    {
        $address = 'testAddress';
        $request = Request::create('warehouse/org', 'POST', ['address' => $address]);
        $request->attributes->set('org_id', $this->_organization['id']);
        (new OrgCreate())->create($request);
        $id = Warehouse::firstWhere('address', $address)['id'];

        $call = $this->get('warehouse/org/?' . 'org_id=' . $this->_organization['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(200);
        $call->seeJsonEquals([[
                'id' => $this->_organizationWarehouse['id'],
                'address' => $this->_organizationWarehouse['address']
            ], [
                'id' => $id,
                'address' => $address
            ]
        ]);
    }
}
