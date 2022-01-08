<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\House\Warehouse;
use App\Models\Org\Organization;
use App\Models\House\OrgHouse;

class HouseAuthTest extends TestCase
{
    private string $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
        $this->endpoint = 'warehouse/auth/?org_id=' . $this->_organization['id'];
    }

    /**
     * Tests required data inputs
     *
     * @return void
     */
    public function test_house_midl_auth_422()
    {
        $call = $this->get($this->endpoint, [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests warehouse authorization for client's warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_pass_1()
    {
        $call = $this->get($this->endpoint . '&house_id=' . $this->_userWarehouse['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('user', $call->response->content());
    }

    /**
     * Tests admin access to user's warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_pass_2()
    {
        $call = $this->get($this->endpoint . '&house_id=' . $this->_userWarehouse['id'], [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('user', $call->response->content());
    }

    /**
     * Tests response on non-existing warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_404()
    {
        $hosueId = intval(Warehouse::orderByDesc('id')->first()['id']) + 1;
        $call = $this->get($this->endpoint . '&house_id=' . $hosueId, [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);
        $call->assertResponseStatus(404);
        $this->assertEquals('warehouse not found', $call->response->content());
    }

    /**
     * Tests user's access to other user's warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_403_1()
    {
        $call = $this->get($this->endpoint . '&house_id=' . $this->_adminWarehouse['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests user access to organization's warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_pass_3()
    {
        $call = $this->get($this->endpoint . '&house_id=' . $this->_organizationWarehouse['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(200);
        $this->assertEquals('organization', $call->response->content());
    }

    /**
     * Tests user access to foreign organization's warehouse
     *
     * @return void
     */
    public function test_house_midl_auth_403_2()
    {
        $houseId = Organization::factory()->create()->orgHouses()->first()['warehouse'];
        $call = $this->get($this->endpoint . '&house_id=' . $houseId, [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);
        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }
}
