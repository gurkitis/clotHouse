<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\ClothingUnit;

class UnitHouseIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful warehouse's clothing units data retrieval
     *
     * @return void
     */
    public function test_unit_house_index_200()
    {
        $call = $this->get('clothing/unit/warehouse/'
            . '?org_id=' . $this->_organization['id']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals(ClothingUnit::where('warehouse', $this->_organizationWarehouse['id'])->get()->toArray());
    }
}
