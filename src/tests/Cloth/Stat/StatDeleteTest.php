<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\Cloth\Status;

class StatDeleteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->setVariables();
    }

    /**
     * Tests deleting status without dependencies
     *
     * @return void
     */
    public function test_stat_delete_204()
    {
        $statId = Status::factory()->create(['organization' => $this->_organization['id']])['id'];
        $call = $this->delete('clothing/status/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $statId,
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(204);
        $this->notSeeInDatabase('status', [
            'id' => $statId,
            'organization' => $this->_organization['id']
        ]);
    }

    /**
     * Tests deleting status with dependencies
     *
     * @return void
     */
    public function test_stat_delete_400()
    {
        $call = $this->delete('clothing/status/'
            . '?org_id=' . $this->_organization['id']
            . '&stat_id=' . $this->_clothingUnits[0]['status'],
            [],
            ['Authorization' => 'Bearer ' . $this->_adminBearer]
        );

        $call->assertResponseStatus(400);
        $this->assertEquals('cannot delete, entity has dependency', $call->response->content());
        $this->seeInDatabase('status', [
            'id' => $this->_clothingUnits[0]->status()['id'],
            'organization' => $this->_organization['id']
        ]);
    }
}
