<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\ClothingUnit;

class UnitCreateTest extends TestCase
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
    public function test_unit_create_422()
    {
        $call = $this->post('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status']
            . '&cloth_id=' . $this->_clothingUnits[0]['clothing']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['identificator' => $this->_clothingUnits[0]['identificator']],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests creating clothing unit in user's warehouse
     *
     * @return void
     */
    public function test_unit_create_400()
    {
        $call = $this->post('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status']
            . '&cloth_id=' . $this->_clothingUnits[0]['clothing']
            . '&house_id=' . $this->_userWarehouse['id'],
            ['identificator' => ClothingUnit::factory()->make()['identificator']],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );
        $this->assertEquals(400, $call->response->status(), $call->response->content());
        $call->assertResponseStatus(400);
        $this->assertEquals('cannot initialize in user warehouse', $call->response->content());
    }

    /**
     * Tests successful clothing unit creation
     *
     * @return void
     */
    public function test_unit_create_201()
    {
        $identificator = ClothingUnit::factory()->make()['identificator'];
        $call = $this->post('clothing/unit/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status']
            . '&cloth_id=' . $this->_clothingUnits[0]['clothing']
            . '&house_id=' . $this->_organizationWarehouse['id'],
            ['identificator' => $identificator],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(201);
        $this->seeInDatabase('clothing_unit', [
            'identificator' => $identificator,
            'status' => $this->_clothingUnits[0]['status'],
            'clothing' => $this->_clothingUnits[0]['clothing'],
            'warehouse' => $this->_organizationWarehouse['id'],
            'organization' => $this->_organization['id']
        ]);
        $unitId = ClothingUnit::orderByDesc('id')->first()['id'];
        $this->seeInDatabase('exchange', [
            'information' => "clothing unit's initialization",
            'clothing_unit' => $unitId,
            'issuer_warehouse' => NULL,
            'receiver_warehouse' => $this->_organizationWarehouse['id'],
            'facilitator' => $this->_admin['id']
        ]);
    }
}
