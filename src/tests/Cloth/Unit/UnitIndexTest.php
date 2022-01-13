<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\ClothingUnit;

class UnitIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests clothing unit data retrieval specifying unit's id
     *
     * @return void
     */
    public function test_unit_index_200_1()
    {
        $call = $this->get('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );
        $this->assertEquals(200, $call->response->status(), $call->response->content());
        $call->assertResponseStatus(200);
        $call->seeJsonEquals($this->_clothingUnits[0]->toArray());
    }

    /**
     * Tests clothing unit data retrieval not specifying unit's id
     *
     * @return void
     */
    public function test_unit_index_200_2()
    {
        $this->_clothingUnits[0]->update(['warehouse' => $this->_userWarehouse['id']]);
        $this->_clothingUnits[2]->update(['warehouse' => $this->_userWarehouse['id']]);
        $call = $this->get('clothing/unit/'
            . '?org_id=' . $this->_organization['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals(ClothingUnit::where('warehouse', $this->_userWarehouse['id'])->get()->toArray());
    }
}
