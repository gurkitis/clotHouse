<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Status;

class StatAuthTest extends TestCase
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
    public function test_stat_midl_auth_422()
    {
        $call = $this->get('clothing/status/auth/?org_id='. $this->_organization['id'], [
            'Authorization' => 'Bearer ' .$this->_userBearer
        ]);

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests authorization on non-existing status
     *
     * @return void
     */
    public function test_stat_midl_auth_404()
    {
        $statId = intval(Status::orderByDesc('id')->first()['id']) + 1;
        $call = $this->get('clothing/status/auth'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $statId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(404);
        $this->assertEquals('status not found', $call->response->content());
    }

    /**
     * Tests authorization on foreign organization's status
     *
     * @return void
     */
    public function test_stat_midl_auth_403()
    {
        $statId = Status::factory()->create()['id'];
        $call = $this->get('clothing/status/auth'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $statId,
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(403);
        $this->assertEquals('access denied', $call->response->content());
    }

    /**
     * Tests successful status authorization
     *
     * @return void
     */
    public function test_stat_midl_auth_pass()
    {
        $call = $this->get('clothing/status/auth'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status'],
            ['Authorization' => 'Bearer ' . $this->_userBearer]
        );

        $call->assertResponseStatus(200);
        $this->assertEquals('status resource authorized', $call->response->content());
    }
}
