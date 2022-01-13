<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\House\OrgHouse;
use App\Models\House\Warehouse;
use App\Models\Cloth\ClothingUnit;

class HouseOrgDeleteTest extends TestCase
{
    /**
     * @var Warehouse
     */
    private Warehouse $orgHosue2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests deleting last organization's warehouse
     *
     * @return void
     */
    public function test_house_org_delete_400()
    {
        $call = $this->delete('warehouse/org/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            [],
            ['Authorization' => 'Bearer ' .$this->_ownerBearer]
        );

        $call->assertResponseStatus(400);
        $this->assertEquals('cannot delete last warehouse, please delete organization', $call->response->content());
    }

    /**
     * Tests deleting organization's warehouse specifying non-existing transfer warehouse
     *
     * @return void
     */
    public function test_house_org_delete_404()
    {
        $this->setSecondOrgHouse();
        $houseId = intval(Warehouse::orderByDesc('id')->first()['id']) + 1;
        $call = $this->delete('warehouse/org/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['transfer_house' => $houseId],
            ['Authorization' => 'Bearer ' .$this->_ownerBearer]
        );

        $call->assertResponseStatus(404);
        $this->assertEquals('warehouse not found', $call->response->content());
    }

    /**
     * Tests deleting organization's warehouse specifying foreign organization's transfer warehouse
     *
     * @return void
     */
    public function test_house_org_delete_403()
    {
        $this->setSecondOrgHouse();
        $houseId = OrgHouse::factory()->create()['warehouse'];
        $call = $this->delete('warehouse/org/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['transfer_house' => $houseId],
            ['Authorization' => 'Bearer ' . $this->_ownerBearer]
        );

        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests deleting organization's warehouse not specifying transfer warehouse
     *
     * @return void
     */
    public function test_house_org_delete_204_1()
    {
        $orgHouseId = OrgHouse::firstWhere('warehouse', $this->_organizationWarehouse['id'])['id'];
        $this->setSecondOrgHouse();
        $call = $this->delete('warehouse/org/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_ownerBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('organization_warehouse', ['id' => $orgHouseId]);
        $this->notSeeInDatabase('warehouse', ['id' => $this->_organizationWarehouse['id']]);
        $this->notSeeInDatabase('clothing_unit', ['warehouse' => $this->_organizationWarehouse['id']]);
    }

    /**
     * Tests deleting organization's warehouse specifying transfer warehouse
     *
     * @return void
     */
    public function test_house_org_delete_204_2()
    {
        $orgHouseId = OrgHouse::firstWhere('warehouse', $this->_organizationWarehouse['id'])['id'];
        $this->setSecondOrgHouse();
        $call = $this->delete('warehouse/org/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['transfer_house' => $this->orgHosue2['id']],
            ['Authorization' => 'Bearer ' . $this->_ownerBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('organization_warehouse', ['id' => $orgHouseId]);
        $this->notSeeInDatabase('warehouse', ['id' => $this->_organizationWarehouse['id']]);
        $this->notSeeInDatabase('clothing_unit', ['warehouse' => $this->_organizationWarehouse['id']]);
        $this->assertEquals($this->orgHosue2['id'], ClothingUnit::find($this->_clothingUnits[1]['id'])['warehouse']);
        $this->assertEquals($this->orgHosue2['id'], ClothingUnit::find($this->_clothingUnits[2]['id'])['warehouse']);
        $this->assertEquals($this->_userWarehouse['id'], $this->_clothingUnits[0]['warehouse']);
    }

    private function setSecondOrgHouse(): void
    {
        $this->orgHosue2 = OrgHouse::factory()->create([
            'organization' => $this->_organization['id']
        ])->warehouse();
    }
}
