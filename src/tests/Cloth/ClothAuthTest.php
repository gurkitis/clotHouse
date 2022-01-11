<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Clothing;

class ClothAuthTest extends TestCase
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
    public function test_cloth_midl_auth_422()
    {
        $call = $this->get('clothing/auth/'
            . '?org_id=' . $this->_organization['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests authorizing non-existing clothing
     *
     * @return void
     */
    public function test_cloth_midl_auth_404()
    {
        $clothId = intval(Clothing::orderByDesc('id')->first()['id']) + 1;
        $call = $this->get('clothing/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $clothId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(404);
        $this->assertEquals('clothing not found', $call->response->content());
    }

    /**
     * Tests access to foreign organization's clothing
     *
     * @return void
     */
    public function test_cloth_midl_auth_403()
    {
        $clothId = Clothing::factory()->create()['id'];
        $call = $this->get('clothing/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $clothId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests successful clothing resource authorization
     *
     * @return void
     */
    public function test_cloth_midl_auth_pass()
    {
        $call = $this->get('clothing/auth/'
            . '?org_id=' . $this->_organization['id']
            . '&cloth_id=' . $this->_clothingUnits[0]->clothing()['id'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $this->assertEquals('clothing resource authorized', $call->response->content());
    }
}
