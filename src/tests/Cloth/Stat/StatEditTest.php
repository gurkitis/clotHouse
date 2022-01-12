<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Status;

class StatEditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests successful status name edit
     *
     * @return void
     */
    public function test_stat_edit_200_1()
    {
        $status = Status::factory()->make()['status'];
        $call = $this->put('clothing/status/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status'],
            ['status' => $status],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]->status()['id'],
            'status' => $status,
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('status', [
            'id' => $this->_clothingUnits[0]->status()['id'],
            'status' => $status,
            'organization' => $this->_organization['id']
        ]);
    }

    /**
     * Tests successful status edit
     *
     * @return void
     */
    public function test_stat_edit_200_2()
    {
        $call = $this->put('clothing/status/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(200);
        $call->seeJsonEquals([
            'id' => $this->_clothingUnits[0]->status()['id'],
            'status' => $this->_clothingUnits[0]->status()['status'],
            'organization' => $this->_organization['id']
        ]);
        $this->seeInDatabase('status', [
            'id' => $this->_clothingUnits[0]->status()['id'],
            'status' => $this->_clothingUnits[0]->status()['status'],
            'organization' => $this->_organization['id']
        ]);
    }
}
