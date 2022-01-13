<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\ClothingUnit;

class UnitEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests required input data fields
     *
     * @return void
     */
    public function tests_unit_edit_422()
    {
        $call = $this->put('clothing/unit/'
            . '?org_id=' . $this->_organization['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful edit of no fields
     *
     * @return void
     */
    public function tests_unit_edit_200_1()
    {
        $call = $this->put('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals($this->_clothingUnits[0]->toArray());
        $this->seeInDatabase('clothing_unit', $this->_clothingUnits[0]->toArray());
    }

    /**
     * Tests successful edit of status and identificator fields
     *
     * @return void
     */
    public function tests_unit_edit_200_2()
    {
        $id = ClothingUnit::factory()->make()['identificator'];
        $call = $this->put('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            [
                'status' => $this->_clothingUnits[1]['status'],
                'identificator' => $id
            ],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]['id'],
            'identificator' => $id,
            'status' => $this->_clothingUnits[1]['status'],
            'clothing' => $this->_clothingUnits[0]['clothing'],
            'warehouse' => $this->_userWarehouse['id'],
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('clothing_unit', [
            'id' => $this->_clothingUnits[0]['id'],
            'identificator' => $id,
            'status' => $this->_clothingUnits[1]['status'],
            'clothing' => $this->_clothingUnits[0]['clothing'],
            'warehouse' => $this->_userWarehouse['id'],
            'organization' => $this->_organization['id']
        ]);
    }
}
