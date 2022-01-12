<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Status;

class StatCreateTest extends TestCase
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
    public function test_stat_create_422()
    {
        $call = $this->post('clothing/status/?org_id=' . $this->_organization['id'], [], [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(422);
        $this->assertEquals('invalid input data', $call->response->content());
    }

    /**
     * Tests successful status creation
     *
     * @return void
     */
    public function test_stat_create_201()
    {
        $status = Status::factory()->make()['status'];
        $call = $this->post('clothing/status/?org_id=' . $this->_organization['id'], [
            'status' => $status
        ], [
            'Authorization' => 'Bearer ' . $this->_adminBearer
        ]);

        $call->assertResponseStatus(201);
        $this->seeInDatabase('status', [
            'status' => $status,
            'organization' => $this->_organization['id']
        ]);
    }
}
