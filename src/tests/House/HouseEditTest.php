<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Org\Organization;

class HouseEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests user editing foreign organization's warehouse
     *
     * @return void
     */
    public function test_house_edit_403_1()
    {
        $orgHouseId = Organization::factory()->create()->orgHouses()->first()['warehouse'];
        $call = $this->put('warehouse/?'
            . 'org_id=' . $this->_organization['id']
            . '&house_id=' . $orgHouseId,
            [],
            [ 'Authorization' => 'Bearer ' . $this->_adminBearer ]
        );
        $call->assertResponseStatus(403);
        $this->assertEquals('user access denied', $call->response->content());
    }

    /**
     * Tests user editing organization's warehouse
     *
     * @return void
     */
    public function test_house_edit_403_2()
    {
        $call = $this->put('warehouse/?'
            . 'org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            [],
            [ 'Authorization' => 'Bearer ' . $this->_userBearer ]
        );
        $call->assertResponseStatus(403);
        $this->assertEquals('user access denied', $call->response->content());
    }

    /**
     * Tests user editing its warehouse
     *
     * @return void
     */
    public function test_house_edit_200_1()
    {
        $address = 'testName';
        $call = $this->put('warehouse/?'
            . 'org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_userWarehouse['id'],
            [ 'address' => $address ],
            [ 'Authorization' => 'Bearer ' . $this->_userBearer ]
        );
        $call->assertResponseStatus(200);
        $this->seeInDatabase('warehouse', [
            'id' => $this->_userWarehouse['id'],
            'address' => $address
        ]);
        $call->seeJsonEquals([
            'id' => $this->_userWarehouse['id'],
            'address' => $address
        ]);
    }

    /**
     * Tests admin editing organization's warehouse
     *
     * @return void
     */
    public function test_house_edit_200_2()
    {
        $address = 'testName';
        $call = $this->put('warehouse/?'
            . 'org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            [ 'address' => $address ],
            [ 'Authorization' => 'Bearer ' . $this->_adminBearer ]
        );
        $call->assertResponseStatus(200);
        $this->seeInDatabase('warehouse', [
            'id' => $this->_organizationWarehouse['id'],
            'address' => $address
        ]);
        $call->seeJsonEquals([
            'id' => $this->_organizationWarehouse['id'],
            'address' => $address
        ]);
    }
}
