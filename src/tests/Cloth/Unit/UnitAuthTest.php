<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\ClothingUnit;

class UnitAuthTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests required input data
     *
     * @return void
     */
    public function test_unit_midl_auth_422()
    {
        $call = $this->get('clothing/unit/auth/?org_id=' . $this->_organization['id'], [
            'Authorization' => 'Bearer ' . $this->_userBearer
        ]);

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests authorizing non-existing clothing unit
     *
     * @return void
     */
    public function test_unit_midl_auth_404()
    {
        $unitId = intval(ClothingUnit::orderByDesc('id')->first()['id']) + 1;
        $call = $this->get('clothing/unit/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $unitId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(404);
        $this->assertEquals('clothing unit not found', $call->response->content());
    }

    /**
     * Tests authorizing foreign organization's clothing unit
     *
     * @return void
     */
    public function test_unit_midl_auth_403()
    {
        $unitId = ClothingUnit::factory()->create()['id'];
        $call = $this->get('clothing/unit/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $unitId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests successful clothing unit's authorization
     *
     * @return void
     */
    public function test_unit_midl_auth_pass()
    {
        $call = $this->get('clothing/unit/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&unit_id=' . $this->_clothingUnits[0]['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $this->assertEquals('clothing unit resource authorized', $call->response->content());
    }
}
